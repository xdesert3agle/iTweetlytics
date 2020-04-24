<?php

namespace App\Http\Controllers;


use App\Follower;
use App\Follow;
use App\Friend;
use App\Report;
use App\TwitterProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {

    public function test() {
        $friendsIds = Friend::where('twitter_profile_id', 286561116)->get()->pluck('id_str')->toArray();
        $dbFollowers = Follower::where('twitter_profile_id', 286561116)->get()->pluck('id_str')->toArray();

        $usersNotFollowingCount = count(array_diff($friendsIds, $dbFollowers));

        $percent = 100 - ($usersNotFollowingCount / count($friendsIds)) * 100;

        dd(round($percent, 2) . "%");
    }
}
