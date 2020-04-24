<?php

namespace App\Jobs;

use App\Friend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class FetchRemainingFriendsLookups implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS = 15;
    const FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($profile) {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $dbFriends = Friend::where('twitter_profile_id', $this->profile->id)
            ->whereNull('screen_name')
            ->get();
        $dbFriendsIds = $dbFriends->pluck('id_str')->toArray();

        $fetchedFriendsLookup = [];
        $needed_lookup_requests = count($dbFriendsIds) / self::FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST;

        for ($i = 0; $i < ceil($needed_lookup_requests); $i++)
            $fetchedFriendsLookup = array_merge($fetchedFriendsLookup, Twitter::getUsersLookup(['user_id' => array_slice($dbFriendsIds, $i * self::FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST, self::FRIENDS_USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        foreach ($fetchedFriendsLookup as $i => $fetchedFriend) {
            $dbFriends[$i]->name = $fetchedFriend->name;
            $dbFriends[$i]->screen_name = $fetchedFriend->screen_name;
            $dbFriends[$i]->profile_image_url = $fetchedFriend->profile_image_url;
            $dbFriends[$i]->save();
        }
    }
}
