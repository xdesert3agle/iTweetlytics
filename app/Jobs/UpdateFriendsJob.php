<?php

namespace App\Jobs;

use App\Follower;
use App\Friend;
use App\Helpers\ApiHelper;
use App\Report;
use App\TwitterProfile;
use App\Unfriend;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFriendsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS = 15;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

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
        $this->deleteOldFriends($dbFriends, $fetchedFriends);

        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get()->pluck('id_str')->toArray();

        if ($this->isLast)
            Report::generateDailyReport($this->profile, $dbFollowers);
    }

    protected function insertNewFriends($dbFriends, $fetchedFriendsIds) {
        $newFriends = array_values(array_diff($fetchedFriendsIds, $dbFriends));

        if (!empty($newFriends)) {
            $rate_limits = Twitter::getAppRateLimit(['format' => 'array']);
            $available_requests = $rate_limits['resources']['users']['/users/lookup']['remaining'];
            $needed_requests = count($newFriends) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;
            $rate_reset_timestamp = $rate_limits['resources']['users']['/users/lookup']['reset'];

            if ($available_requests >= $needed_requests) {
                $fetchedFriendsLookup = $this->getLookupFromIdArray($newFriends);

            } else {

                if ($this->isLast) {
                    $lookup_reset_time = Carbon::createFromTimestamp($rate_reset_timestamp);
                    $delay = $lookup_reset_time->diffInSeconds(Carbon::now());

                    FetchRemainingFriendsLookups::dispatch($this->profile)->delay($delay);
                }
            }

            foreach ($newFriends as $i => $newFriendId) {
                $friend = new Friend;
                $friend->twitter_profile_id = $this->profile->id;
                $friend->id_str = $newFriendId;
                $friend->follows_you = Follower::where([
                    ['twitter_profile_id', $this->profile->id],
                    ['id_str', $newFriendId]
                ])->first() ? true : false;

                if (!empty($fetchedFriendsLookup)) {
                    $friend->name = $fetchedFriendsLookup[$i]->name;
                    $friend->screen_name = $fetchedFriendsLookup[$i]->screen_name;
                    $friend->followers_count = $fetchedFriendsLookup[$i]->followers_count;
                    $friend->profile_image_url = $fetchedFriendsLookup[$i]->profile_image_url;
                    $friend->location = $fetchedFriendsLookup[$i]->location;
                }

                $friend->save();
            }
        }
    }

    protected function deleteOldFriends($dbFriends, $fetchedFriendsIds) {
        $oldFriends = array_diff($dbFriends, $fetchedFriendsIds);

        foreach ($oldFriends as $oldFriendId) {
            $friend = Friend::where('id_str', $oldFriendId)->first();

            $unfriend = new Unfriend;
            $unfriend->twitter_profile_id = $this->profile->id;
            $unfriend->id_str = $friend->id_str;
            $unfriend->name = $friend->name;
            $unfriend->screen_name = $friend->screen_name;
            $unfriend->profile_image_url = $friend->profile_image_url;
            $unfriend->location = $friend->location;
            $unfriend->location = $friend->location;
            $unfriend->save();

            $friend->delete();
        }
    }

    protected function getLookupFromIdArray($array) {
        $fetched_lookups = [];
        $needed_lookup_requests = count($array) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($needed_lookup_requests > 0)
            for ($i = 0; $i < ceil($needed_lookup_requests); $i++)
                $fetched_lookups = array_merge($fetched_lookups, Twitter::getUsersLookup(['user_id' => array_slice($array, $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST, self::USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetched_lookups;
    }
}
