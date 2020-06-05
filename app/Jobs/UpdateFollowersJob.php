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
        $this->fetchFollowers(); // El resultado se guarda en el campo fetched_followers de la clase
        $this->processFollowers();

        // Si es el último job de la cadena se comienza el procesamiento
        if ($this->profile->next_followers_cursor == 0) {
            $this->profile->next_followers_cursor = -1;
            $this->profile->save();

            $this->cleanExfriends();
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
        $remaining_requests_count = ApiHelper::getRateLimit('followers', 'remaining');
        $cursor = $this->profile->next_followers_cursor;
        $count = 0;

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        ApiHelper::reconfig($this->profile);

        do { // Se fetchean los seguidores
            $followers = Twitter::getFollowersIds(['screen_name' => $this->profile->twitter_profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $followers->next_cursor;

            $this->fetched_followers_ids = array_merge($this->fetched_followers_ids, $followers->ids);
        } while ($cursor != 0 && ++$count < $remaining_requests_count); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_followers_cursor = $cursor;
        $this->profile->save();
    }

    protected function processFollowers() {

        // El array se divide en dos chunks, ya que MySQL tiene problemas al manejar un array muy grande
        $split_array = array_chunk($this->fetched_followers_ids, count($this->fetched_followers_ids) / 2);

        foreach ($split_array as $chunk) {
            $fast_updated_ids = Follower::where([['user_profile_id', $this->profile->id], ['is_present', false]])
                ->whereIn('id', $chunk)
                ->pluck('twitter_profile_id')
                ->toArray();

            Follower::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $chunk)
                ->update([
                    'is_present' => true
                ]);

            // Se evalúa a los que no están presentes
            $not_present = array_diff($chunk, $fast_updated_ids);

            foreach ($not_present as $not_present_follower_id) {
                TwitterProfile::insertReducedIfNew($not_present_follower_id);

                Follow::create([
                    'user_profile_id' => $this->profile->id,
                    'twitter_profile_id' => $not_present_follower_id
                ]);

                Follower::create([
                    'user_profile_id' => $this->profile->id,
                    'twitter_profile_id' => $not_present_follower_id,
                    'is_present' => true
                ]);

                $this->updateFriendRelationships($not_present_follower_id, 'follow');
            }
        }
    }

    protected function cleanExfriends() {
        $unfollowers_ids = Follower::where([
            ['user_profile_id', $this->profile->id],
            ['is_present', false],
        ])->get()->pluck('twitter_profile_id');

        foreach ($unfollowers_ids as $unfollower_id) {
            Unfollow::create([
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $unfollower_id
            ]);

            // Se elimina el perfil de la lista de followers
            Follower::where([
                ['user_profile_id', $this->profile->id],
                ['id', $unfollower_id]
            ])->delete();

            $this->updateFriendRelationships($unfollower_id, 'unfollow');
        }
    }

    protected function resetPresentAttribute() {
        Follower::where([
            ['user_profile_id', $this->profile->id],
            ['is_present', true],
        ])->update(['is_present' => false]);
    }

    /*protected function registerFollows($db_followers) {
        $new_followers_ids = array_reverse(array_diff($this->fetched_followers_ids, $db_followers));

        foreach ($new_followers_ids as $new_follower_id) {
            TwitterProfile::insertReducedIfNew($new_follower_id);

            $fields = [
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $new_follower_id
            ];

            // Se inserta el follow
            Follow::create($fields);

            // Se inserta el follower
            Follower::create($fields);

            $this->updateFriendRelationships($new_follower_id, 'follow');
        }
    }*/

    /*protected function registerUnfollows($db_followers) {
        $new_unfollowers = array_diff($db_followers, $this->fetched_followers_ids);
        $new_unfollowers_hydrated = Follower::whereIn('id', $new_unfollowers)->get();

        foreach ($new_unfollowers_hydrated as $unfollower) {

            // Se registra el unfollow
            Unfollow::create([
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $unfollower->id
            ]);

            // Se elimina el usuario de la lista de followers
            Follower::where([
                ['user_profile_id', $this->profile->id],
                ['id', $unfollower->id]
            ])->delete();

            $this->updateFriendRelationships($unfollower->id, 'unfollow');
        }
    }*/

    protected function updateFriendRelationships($id, $action) {
        $value = $action == 'follow' ? true : false;

        $updated = Friend::where([
            'user_profile_id' => $this->profile->id,
            'twitter_profile_id' => $id
        ])->update(['is_following_back' => $value]);

        if ($updated) {
            Befriend::where([
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $id
            ])->update(['is_following_back' => $value]);

            Unfriend::where([
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $id
            ])->update(['is_following_back' => $value]);
        }
    }

    protected function sendOneMoreJob() {
        $next_job_delay_seconds = ApiHelper::getRateLimit('followers', 'reset');
        UpdateFollowersJob::dispatch($this->profile)->delay(now()->addSeconds($next_job_delay_seconds));
    }


    protected function sendUpdateFriendsJob() {
        $needed_friends_jobs = ceil($this->profile->twitter_profile->friends_count / (self::MAX_CONSECUTIVE_REQUESTS * self::IDS_PER_REQUEST));

        $job_info = [
            'id' => 0,
            'last_job_id' => $needed_friends_jobs - 1
        ];

        UpdateFriendsJob::dispatch($this->profile, [], $job_info);
    }

    protected function writeToLog($content) {
        $file = Storage::get('log.txt');
        Storage::put('log.txt', $file . "$content\n");
    }
}
