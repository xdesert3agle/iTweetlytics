<?php

namespace App\Http\Controllers;


use App\Follower;
use App\Follow;
use App\Friend;
use App\Helpers\ApiHelper;
use App\Jobs\FetchRemainingFriendsLookups;
use App\Report;
use App\TwitterProfile;
use App\Unfriend;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {

    public function test() {
        $friend = Friend::where([
            ['twitter_profile_id', 286561116],
            ['id_str', 28955739]
        ])->first();

        $friend->follows_you = true;
        $friend->save();

        dd($friend);
    }
}
