<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Helpers\UtilHelper;
use App\TwitterProfile;


class TestController extends Controller {
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    public function test() {
        $profile = TwitterProfile::find(286561116);
        //$profile->refreshTags([Follower::class]);
        $startTime = microtime(true);
        for ($i = 0; $i < 10; $i++) {
            get_headers("https://t.co/BpDoA9z3Kp", 0);
        }
        dd(microtime(true) - $startTime);
    }
}
