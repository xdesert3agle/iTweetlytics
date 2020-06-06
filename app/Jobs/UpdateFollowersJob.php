<?php

namespace App\Jobs;

use App\Befriend;
use App\Friend;
use App\TwitterProfile;
use App\Follower;
use App\Follow;
use App\Unfollow;
use App\Helpers\ApiHelper;
use App\Unfriend;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;
use Exception;

class UpdateFollowersJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const IDS_PER_REQUEST = 5000;

    protected $profile;
    protected $fetched_followers_ids;

    public function __construct($profile) {
        $this->profile = $profile;
        $this->fetched_followers_ids = [];
    }

    public function handle() {
        $this->fetchFollowers(); // El resultado se guarda en $this->fetched_followers_ids

        if (!empty($this->fetched_followers_ids)) { // Si se han recogido followers se procesan
            $this->processFollowers();
        }

        // Si es el último job de la cadena se comienza el procesamiento
        if ($this->profile->next_followers_cursor == 0) {
            $this->profile->next_followers_cursor = -1;
            $this->profile->save();

            $this->cleanExfollowers();
            $this->resetPresentAttribute();
            $this->sendUpdateFriendsJob();

        } else { // Si no es el último job se manda uno más para que siga fetcheando
            $this->sendOneMoreJob();
        }
    }

    public function failed(Exception $exception) {
        $this->writeToLog($exception->getMessage());
    }

    protected function fetchFollowers() {
        ApiHelper::reconfig($this->profile);

        $remaining_requests_count = ApiHelper::getRateLimit('followers', 'remaining');
        $cursor = $this->profile->next_followers_cursor;
        $count = 0;

        while ($cursor != 0 && $count++ < $remaining_requests_count) {
            $followers = Twitter::getFollowersIds(['screen_name' => $this->profile->twitter_profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $followers->next_cursor;

            $this->fetched_followers_ids = array_merge($this->fetched_followers_ids, $followers->ids);
        }

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_followers_cursor = $cursor;
        $this->profile->save();
    }

    protected function processFollowers() {

        // El array se divide en partes de 10.000 elementos, ya que MySQL tiene problemas al manejar un array muy grande
        $fetched_count = count($this->fetched_followers_ids);
        $chunk_size = $fetched_count < 10000 ? $fetched_count : 10000;

        $split_array = array_chunk($this->fetched_followers_ids, $chunk_size);

        foreach ($split_array as $chunk) {

            // Se "pasa lista" a los records del array que estén en la base de datos
            $early_updated = Follower::where([
                'user_profile_id' => $this->profile->id,
                'is_present' => false
            ])->whereIn('twitter_profile_id', $chunk)
                ->pluck('twitter_profile_id')
                ->all();

            Follower::where([
                'user_profile_id' => $this->profile->id,
                'is_present' => false
            ])->whereIn('twitter_profile_id', $early_updated)
                ->update(['is_present' => true]);

            // Los que no están en la base de datos son seguidores nuevos
            $new_followers = array_diff($chunk, $early_updated);
            unset($early_updated);

            if (!empty($new_followers)) {

                // Se obtienen los nuevos TwitterProfiles descartando los que no son nuevos
                $already_inserted_profiles = TwitterProfile::whereIn('id', $new_followers)->pluck('id')->toArray();
                $new_twitter_profiles_ids = array_diff($new_followers, $already_inserted_profiles);
                unset($already_inserted_profiles);

                if (!empty($new_twitter_profiles_ids)) {
                    foreach ($new_twitter_profiles_ids as $key => $new_twitter_profile_id) {
                        $f_new_profiles[] = [
                            'id' => $new_twitter_profile_id,
                            'created_at' => Carbon::now()
                        ];
                    }

                    TwitterProfile::insert($f_new_profiles); // Bulk insert de todos los nuevos TwitterProfiles
                    unset($f_new_profiles);
                }

                // Se formatea el array para la inserción masiva de Follows y Followers
                foreach ($new_followers as $key => $new_twitter_profiles_id) {
                    $f_new_followers[] = [
                        'user_profile_id' => $this->profile->id,
                        'twitter_profile_id' => $new_twitter_profiles_id,
                        'created_at' => Carbon::now()
                    ];
                }

                Follow::insert($f_new_followers);
                Follower::insert($f_new_followers);
                $this->updateFriendsFollowingState($new_followers, 'follow');
                unset($f_new_followers);
            }
        }
    }

    protected function cleanExfollowers() {
        $unfollowers_profile_id = Follower::where([
            'user_profile_id' => $this->profile->id,
            'is_present' => false,
        ])->get()->pluck('twitter_profile_id')->all();

        if (!empty($unfollowers_profile_id)) {
            foreach ($unfollowers_profile_id as $unfollower_profile_id) {
                $f_unfollowers[] = [
                    'user_profile_id' => $this->profile->id,
                    'twitter_profile_id' => $unfollower_profile_id,
                    'created_at' => Carbon::now()
                ];
            }

            Unfollow::insert($f_unfollowers);

            Follower::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $unfollowers_profile_id)
                ->delete();

            $this->updateFriendsFollowingState($unfollowers_profile_id, 'unfollow');
        }
    }

    protected function resetPresentAttribute() {
        Follower::where([
            ['user_profile_id', $this->profile->id],
            ['is_present', true],
        ])->update(['is_present' => false]);
    }

    protected function updateFriendsFollowingState($pool, $action) {
        $value = $action == 'follow' ? true : false;

        Friend::where('user_profile_id', $this->profile->id)
            ->whereIn('twitter_profile_id', $pool)
            ->update(['is_following_back' => $value]);

        Befriend::where('user_profile_id', $this->profile->id)
            ->whereIn('twitter_profile_id', $pool)
            ->update(['is_following_back' => $value]);

        Unfriend::where('user_profile_id', $this->profile->id)
            ->whereIn('twitter_profile_id', $pool)
            ->update(['is_following_back' => $value]);
    }

    protected function sendOneMoreJob() {
        $next_job_delay_seconds = ApiHelper::getRateLimit('followers', 'reset');
        UpdateFollowersJob::dispatch($this->profile)->delay(now()->addSeconds($next_job_delay_seconds));
    }

    protected function sendUpdateFriendsJob() {
        UpdateFriendsJob::dispatch($this->profile);
    }

    protected function writeToLog($content) {
        $file = Storage::get('log.txt');
        Storage::put('log.txt', $file . "$content\n");
    }
}
