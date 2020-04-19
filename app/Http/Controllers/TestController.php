<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Jobs\UpdateFollowersAndUnfollowers;
use App\TwitterProfile;
use App\Unfollower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    function test() {
        //
    }
}
