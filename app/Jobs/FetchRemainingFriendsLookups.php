<?php

namespace App\Jobs;

use App\Friend;
use App\Helpers\ApiHelper;
use App\TwitterProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class FetchRemainingFriendsLookups implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS = 15;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $db_friends_ids = TwitterProfile::where('added_by', $this->profile->id)
            ->whereNull('screen_name')
            ->get()
            ->pluck('id_str')
            ->toArray();

        ApiHelper::reconfig($this->profile);

        $fetched_friends_lookups = [];
        $needed_lookup_requests = count($db_friends_ids) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;

        for ($i = 0; $i < ceil($needed_lookup_requests); $i++)
            $fetched_friends_lookups = $this->getLookupsFromIdArray($db_friends_ids);

        foreach ($fetched_friends_lookups as $i => $fetched_friend) {
            TwitterProfile::where('id', $fetched_friend->id_str)
                ->update([
                    'name' => $fetched_friend->name,
                    'screen_name' => $fetched_friend->screen_name,
                    'description' => $fetched_friend->description,
                    'url' => $fetched_friend->url,
                    'location' => $fetched_friend->location,
                    'friends_count' => $fetched_friend->friends_count,
                    'followers_count' => $fetched_friend->followers_count,
                    'statuses_count' => $fetched_friend->statuses_count,
                    'listed_count' => $fetched_friend->listed_count,
                    'profile_image_url' => $fetched_friend->profile_image_url,
                    'profile_banner_url' => $fetched_friend->profile_banner_url,
                    'protected' => $fetched_friend->protected,
                    'verified' => $fetched_friend->verified,
                    'suspended' => 0,
                    'lang' => $fetched_friend->lang
                ]);
        }
    }

    protected function getLookupsFromIdArray($array) {
        $fetched_lookups = [];
        $needed_lookup_requests = count($array) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($needed_lookup_requests > 0)
            for ($i = 0; $i < ceil($needed_lookup_requests); $i++)
                $fetched_lookups = array_merge($fetched_lookups, Twitter::getUsersLookup(['user_id' => array_slice($array, $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST, self::USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetched_lookups;
    }
}
