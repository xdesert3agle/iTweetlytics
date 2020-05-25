<?php

namespace App\Jobs;

use App\Follower;
use App\Follow;
use App\Friend;
use App\Helpers\ApiHelper;
use App\TwitterProfile;
use App\Url;
use App\SyncedProfile;
use App\Tag;
use App\Unfollow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const IDS_PER_REQUEST = 5000;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;
    protected $is_last_job;

    public function __construct($profile, $is_last_job) {
        $this->profile = $profile;
        $this->is_last_job = $is_last_job;
    }

    public function handle() {
        $db_followers = Follower::where('synced_profile_id', $this->profile->id)->get()->pluck('twitter_profile_id')->toArray();
        $cursor = $this->profile->next_followers_cursor;
        $count = 0;
        $fetchedFollowers = [];

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        ApiHelper::reconfig($this->profile);

        // Se fetchean los seguidores
        do {
            $followers = Twitter::getFollowersIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $followers->next_cursor;

            $fetchedFollowers = array_merge($fetchedFollowers, $followers->ids);
        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_followers_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();

        $this->registerFollows($db_followers, $fetchedFollowers);
        $this->registerUnfollows($db_followers, $fetchedFollowers);

        if ($this->is_last_job) {
            $needed_friends_jobs = ceil($this->profile->friends_count / (self::MAX_CONSECUTIVE_REQUESTS * self::IDS_PER_REQUEST));

            for ($i = 0; $i < $needed_friends_jobs; $i++) {
                $friends_delay = $i * self::REQUEST_WINDOW;
                $is_last_job = $i == ($needed_friends_jobs - 1) ? true : false;

                UpdateFriendsJob::dispatch($this->profile, $is_last_job)->delay(now()->addMinutes($friends_delay));
            }
        }
    }

    protected function registerFollows($db_followers, $fetched_followers) {
        $new_followers = array_diff($fetched_followers, $db_followers);
        $fetched_users_lookup = array_reverse($this->getFetchedUsersLookup($new_followers));

        foreach ($fetched_users_lookup as $new_follower) {
            TwitterProfile::insertIfNew($this->profile, $new_follower);
            Url::insertProfileUrls($new_follower);

            $fields = [
                'synced_profile_id' => $this->profile->id,
                'twitter_profile_id' => $new_follower->id_str,
                'tags' => SyncedProfile::getTagsFromProfile($this->profile, $new_follower)
            ];

            // Se inserta el follow
            Follow::create($fields);

            // Se inserta el follower
            Follower::create($fields);
        }
    }

    protected function registerUnfollows($db_followers, $fetched_followers) {
        $new_unfollowers = array_diff($db_followers, $fetched_followers);
        $new_unfollowers_hydrated = Follower::whereIn('id', $new_unfollowers)->get();

        foreach ($new_unfollowers_hydrated as $unfollower) {

            // Se registra el unfollow
            Unfollow::create([
                'synced_profile_id' => $this->profile->id,
                'twitter_profile_id' => $unfollower->id,
                'tags' => $unfollower->tags
            ]);

            // Se elimina el usuario de la lista de followers
            Follower::where([
                ['synced_profile_id', $this->profile->id],
                ['id', $unfollower->id]
            ])->delete();
        }
    }

    protected function getFetchedUsersLookup($users_array) {
        $fetched_users_lookup = [];
        $needed_lookups_request_count = count($users_array) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($needed_lookups_request_count > 0)
            for ($i = 0; $i < ceil($needed_lookups_request_count); $i++)
                $fetched_users_lookup = array_merge($fetched_users_lookup, Twitter::getUsersLookup(['user_id' => array_slice($users_array, $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST, self::USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetched_users_lookup;
    }
}
