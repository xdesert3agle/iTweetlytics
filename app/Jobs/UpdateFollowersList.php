<?php

namespace App\Jobs;

use App\Follower;
use App\TwitterProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersList implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    protected $twitterProfile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($twitterProfile) {
        $this->twitterProfile = $twitterProfile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {/*
        $cursor = $this->twitterProfile->next_followers_cursor;
        $cursor = -1;

        if ($cursor == 0)
            $cursor = -1;

        $count = 0;

        Twitter::reconfig([
            "token" => $this->twitterProfile->oauth_token,
            "secret" => $this->twitterProfile->oauth_token_secret,
        ]);

        $followers = [];

        do {
            $response = Twitter::getFollowersIds(['screen_name' => $this->twitterProfile->screen_name, 'cursor' => $cursor]);
            $cursor = $response->next_cursor;

            foreach ($response->ids as $id) {
                /*$follower = new Follower;
                $follower->twitter_profiles_id = $this->twitterProfile->id;
                $follower->twitter_user_id = $id;
                $follower->save();
                $followers[$count][] = $id;
            }
        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS);

        dd($followers);*/
    }
}
