<?php

namespace App\Jobs;

use App\Follower;
use App\Friend;
use App\Follow;
use App\Helpers\ApiHelper;
use App\Report;
use App\Unfollow;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFriends implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS = 15;
    const FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;
    protected $isLast;

    public function __construct($profile, $isLast) {
        $this->profile = $profile;
        $this->isLast = $isLast;
    }

    public function handle() {
        $dbFriends = Friend::where('twitter_profile_id', $this->profile->id)->get()->pluck('id_str')->toArray();
        $cursor = $this->profile->next_friends_cursor;
        $count = 0;
        $fetchedFriends = [];

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        ApiHelper::reconfig($this->profile);

        // Se fetchean los seguidos
        do {
            $friends = Twitter::getFriendsIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $friends->next_cursor;

            $fetchedFriends = array_merge($fetchedFriends, $friends->ids);
        } while ($cursor != 0 && ++$count < self::FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_friends_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();

        $this->insertNewFriends($dbFriends, $fetchedFriends);

        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get()->pluck('id_str')->toArray();

        if ($this->isLast)
            Report::generateDailyReport($this->profile, $dbFollowers);
    }

    protected function insertNewFriends($dbFriends, $fetchedFriendsIds) {
        $newFriends = array_diff($fetchedFriendsIds, $dbFriends);

        //$rateLimits = Twitter::getAppRateLimits(['format' => 'array']);
        //$availableLookupRequests = $rateLimits['resources']['users']['/users/lookup']['remaining'];
        //$usersLookupResetTime = $rateLimits['resources']['users']['/users/lookup']['reset'];

        /*if ($availableLookupRequests >= count($newFriends)) {
            $fetchedFriendsLookup = $this->getFetchedFriendsLookup($newFriends);
        } else {
            if ($this->isLast) {
                FetchRemainingFriendsLookups::dispatch($this->profile)->delay(Carbon::createFromTimestamp($usersLookupResetTime));
            }
        }*/

        foreach ($newFriends as $newFriendId) {
            $friend = new Friend;
            $friend->twitter_profile_id = $this->profile->id;
            $friend->id_str = $newFriendId;
            $friend->save();
        }
    }

    protected function getFetchedFriendsLookup($newFriends) {
        $fetched_friends_lookup = [];
        $needed_lookup_requests = count($newFriends) / self::FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($needed_lookup_requests > 0)
            for ($i = 0; $i < ceil($needed_lookup_requests); $i++)
                $fetched_friends_lookup = array_merge($fetched_friends_lookup, Twitter::getUsersLookup(['user_id' => array_slice($newFriends, $i * self::FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST, self::FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetched_friends_lookup;
    }
}
