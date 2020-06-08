<?php

namespace App\Jobs;

use App\Follow;
use App\Follower;
use App\TwitterProfile;
use App\Befriend;
use App\Friend;
use App\Unfollow;
use App\Unfriend;
use App\Helpers\ApiHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFriendsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const IDS_PER_REQUEST = 5000;

    protected $profile;
    protected $fetched_friends_ids;

    public function __construct($profile) {
        $this->profile = $profile;
        $this->fetched_friends_ids = [];
    }

    public function handle() {
        $this->fetchFriends(); // El resultado se guarda en $this->fetched_friends_ids

        if (!empty($this->fetched_friends_ids)) { // Si se han recogido seguidos se procesan
            $this->processFriends();
        }

        // Si es el último job de la cadena se comienza el procesamiento
        if ($this->profile->next_friends_cursor == 0) {
            $this->profile->next_friends_cursor = -1;
            $this->profile->save();

            $this->cleanExfriends();
            $this->resetPresentAttribute();
            $this->sendLookupsJob();

        } else { // Si no es el último job se manda uno más para que siga fetcheando
            $this->sendOneMoreJob();
        }
    }

    public function failed(Exception $exception) {
        $this->writeToLog($exception->getMessage());
    }

    protected function fetchFriends() {
        ApiHelper::reconfig($this->profile);

        $remaining_requests_count = ApiHelper::getRateLimit('friends', 'remaining');

        if ($remaining_requests_count > 0) {
            $cursor = $this->profile->next_friends_cursor;
            $count = 0;

            while ($cursor != 0 && $count++ < $remaining_requests_count) {
                $friends = Twitter::getFriendsIds(['screen_name' => $this->profile->twitter_profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
                $cursor = $friends->next_cursor;

                $this->fetched_friends_ids = array_merge($this->fetched_friends_ids, $friends->ids);
            }

            // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
            $this->profile->next_friends_cursor = $cursor;
            $this->profile->save();
        }
    }

    protected function processFriends() {

        // El array se divide en partes de 10.000 elementos, ya que MySQL tiene problemas al manejar un array muy grande
        $fetched_count = count($this->fetched_friends_ids);
        $chunk_size = $fetched_count < 10000 ? $fetched_count : 10000;

        $split_array = array_chunk($this->fetched_friends_ids, $chunk_size);

        foreach ($split_array as $i => $chunk) {

            // Se "pasa lista" a los records del array que estén en la base de datos
            $early_updated = Friend::where([
                'user_profile_id' => $this->profile->id,
                'is_present' => false
            ])->whereIn('twitter_profile_id', $chunk)
                ->pluck('twitter_profile_id')
                ->all();

            Friend::where([
                'user_profile_id' => $this->profile->id,
                'is_present' => false
            ])->whereIn('twitter_profile_id', $early_updated)
                ->update(['is_present' => true]);

            // Los que no están en la base de datos son seguidores nuevos
            $new_friends = array_diff($chunk, $early_updated);
            unset($early_updated);

            if (!empty($new_friends)) {
                $new_friends = array_reverse($new_friends);

                // Se obtienen los nuevos TwitterProfiles descartando los que no son nuevos
                $already_inserted_profiles = TwitterProfile::whereIn('id', $new_friends)->pluck('id')->toArray();
                $new_twitter_profiles_ids = array_diff($new_friends, $already_inserted_profiles);
                unset($already_inserted_profiles);

                if (!empty($new_twitter_profiles_ids)) {
                    foreach ($new_twitter_profiles_ids as $new_twitter_profile_id) {
                        $f_new_profiles[] = [
                            'id' => $new_twitter_profile_id,
                            'created_at' => Carbon::now()
                        ];
                    }

                    TwitterProfile::insert($f_new_profiles); // Bulk insert de todos los nuevos TwitterProfiles
                    unset($f_new_profiles);
                }

                // Se formatea el array para realizar la inserción masiva
                foreach ($new_friends as $new_twitter_profiles_id) {
                    $f_new_friends[] = [
                        'user_profile_id' => $this->profile->id,
                        'twitter_profile_id' => $new_twitter_profiles_id,
                        'created_at' => Carbon::now()
                    ];
                }

                Befriend::insert($f_new_friends);
                Friend::insert($f_new_friends);
                $this->updateFollowersFollowingState($new_friends, 'follow');
                unset($f_new_friends);
            }
        }
    }

    protected function cleanExfriends() {
        $unfriends_profile_ids = Friend::where([
            'user_profile_id' => $this->profile->id,
            'is_present' => false,
        ])->get()->pluck('twitter_profile_id')->all();

        if (!empty($unfriends_profile_ids)) {
            foreach ($unfriends_profile_ids as $unfriend_profile_id) {
                $f_unfriends[] = [
                    'user_profile_id' => $this->profile->id,
                    'twitter_profile_id' => $unfriend_profile_id,
                    'created_at' => Carbon::now()
                ];
            }

            Unfriend::insert($f_unfriends);

            Friend::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $unfriends_profile_ids)
                ->delete();

            $this->updateFollowersFollowingState($unfriends_profile_ids, 'unfollow');
        }
    }

    protected function resetPresentAttribute() {
        Friend::where([
            'user_profile_id' => $this->profile->id,
            'is_present' => true
        ])->update(['is_present' => false]);
    }

    protected function updateFollowersFollowingState($pool, $action) {
        $value = $action == 'follow' ? true : false;

        Follower::where('user_profile_id', $this->profile->id)
            ->whereIn('twitter_profile_id', $pool)
            ->update(['is_followed_back' => $value]);

        Follow::where('user_profile_id', $this->profile->id)
            ->whereIn('twitter_profile_id', $pool)
            ->update(['is_followed_back' => $value]);

        Unfollow::where('user_profile_id', $this->profile->id)
            ->whereIn('twitter_profile_id', $pool)
            ->update(['is_followed_back' => $value]);
    }

    protected function sendOneMoreJob() {
        $next_job_delay_seconds = ApiHelper::getRateLimit('friends', 'reset');
        UpdateFriendsJob::dispatch($this->profile)->delay(now()->addSeconds($next_job_delay_seconds));
    }

    protected function sendLookupsJob() {
        UpdateFollowersAndFriendsFollowingStatusJob::dispatch($this->profile);
    }

    protected function writeToLog($content) {
        $file = Storage::get('log.txt');
        Storage::put('log.txt', $file . "$content\n");
    }
}
