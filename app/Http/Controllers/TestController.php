<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Friend;
use App\TwitterProfile;

class TestController extends Controller {
    public function test() {

        $users_following_count = Friend::where('twitter_profile_id', 286561116)
            ->whereIn('id_str', Follower::where('twitter_profile_id', 286561116)->get()->pluck('id_str'))
            ->count();

        dd($users_following_count);

    }
}
