<?php

namespace App\Jobs;

use App\Follower;
use App\Follow;
use App\Friend;
use App\Helpers\ApiHelper;
use App\Unfollow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get()->pluck('id_str')->toArray();
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

        $this->registerFollows($dbFollowers, $fetchedFollowers);
        $this->registerUnfollows($dbFollowers, $fetchedFollowers);

        if ($this->is_last_job) {
            $needed_friends_jobs = ceil($this->profile->friends_count / (self::MAX_CONSECUTIVE_REQUESTS * self::IDS_PER_REQUEST));

            for ($i = 0; $i < $needed_friends_jobs; $i++) {
                $friends_delay = $i * self::REQUEST_WINDOW;
                $is_last_job = $i == ($needed_friends_jobs - 1) ? true : false;

                UpdateFriendsJob::dispatch($this->profile, $is_last_job)->delay(now()->addMinutes($friends_delay));
            }
        }
    }

    protected function registerFollows($dbFollowers, $fetchedFollowers) {
        $newFollowers = array_diff($fetchedFollowers, $dbFollowers);
        $fetchedUsersLookup = array_reverse($this->getFetchedUsersLookup($newFollowers));

        foreach ($fetchedUsersLookup as $user) {

            // Se registra el cambio
            $follow = new Follow;
            $follow->twitter_profile_id = $this->profile->id;
            $follow->id_str = $user->id_str;
            $follow->name = $user->name;
            $follow->screen_name = $user->screen_name;
            $follow->profile_image_url = $user->profile_image_url;
            $follow->save();

            // Nuevo follower a la lista
            $follower = new Follower;
            $follower->twitter_profile_id = $this->profile->id;
            $follower->id_str = $user->id_str;
            $follower->name = $user->name;
            $follower->screen_name = $user->screen_name;
            $follower->profile_image_url = $user->profile_image_url;
            $follower->followers_count = $user->followers_count;
            $follower->location = $user->location;
            $follower->save();
        }
    }

    protected function registerUnfollows($dbFollowers, $fetchedFollowers) {
        $newUnfollowers = array_diff($dbFollowers, $fetchedFollowers);

        $newUnfollowersHydrated = Follower::whereIn('id_str', $newUnfollowers)->get();

        foreach ($newUnfollowersHydrated as $user) {

            // Se registra el unfollow
            $unfollow = new Unfollow;
            $unfollow->twitter_profile_id = $this->profile->id;
            $unfollow->id_str = $user->id_str;
            $unfollow->name = $user->name;
            $unfollow->screen_name = $user->screen_name;
            $unfollow->profile_image_url = $user->profile_image_url;
            $unfollow->save();

            // Se elimina el usuario de la lista de followers
            Follower::where([
                ['twitter_profile_id', $this->profile->id],
                ['id_str', $user->id_str]
            ])->delete();
        }
    }

    protected function getFetchedUsersLookup($newFollowers) {
        $fetchedUsersLookup = [];
        $neededNamesReqCount = count($newFollowers) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($neededNamesReqCount > 0)
            for ($i = 0; $i < ceil($neededNamesReqCount); $i++)
                $fetchedUsersLookup = array_merge($fetchedUsersLookup, Twitter::getUsersLookup(['user_id' => array_slice($newFollowers, $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST, self::USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetchedUsersLookup;
    }
}
