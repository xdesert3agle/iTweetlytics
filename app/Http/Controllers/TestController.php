<?php

namespace App\Http\Controllers;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Helpers\ApiHelper;
use App\TwitterProfile;
use App\Unfollow;
use App\Unfriend;
use App\User;
use App\UserProfile;
use App\UserProfileTaggedProfiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 900;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;
    const MAX_USERS_TAKE_AMOUNT = 1000;

    public function test() {
        $startTime = microtime(true);

        ApiHelper::reconfig(UserProfile::where('id', 1)->first());

        $user = User::create();


        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
    }

    protected function writeToLog($filename, $content) {
        $file = Storage::get($filename);
        Storage::put($filename, $file . "$content\n");
    }
}
