<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Jobs\UpdateFollowersList;
use App\TwitterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    protected function addToLog($string) {
        $file = Storage::get('file.txt');
        Storage::put('file.txt', $file . "$string\n");
    }

    function test() {
        dd(Twitter::getAppRateLimit());
        $followers = Follower::where('twitter_profile_id', '286561116');
        dd($followers->get());
        /*$profile = TwitterProfile::find('286561116');
        $response = Twitter::getFollowersIds(['screen_name' => $profile->screen_name]);

        $isFollower = $followers->firstWhere('twitter_user_id', '541583118') != null;*/


        /*foreach ($response->ids as $id) {
            $followers = Follower::where('twitter_profile_id', $profile->id);
            $this->addToLog($isFollower);
            $isFollower = $followers->firstWhere('twitter_user_id', $id) != null;

            // Se inserta si la ID no está entre los followers
            if (!$isFollower) {
                $follower = new Follower;
                $follower->twitter_profile_id = $profile->id;
                $follower->twitter_user_id = $id;
                $follower->save();
            }
        }*/
    }
}
