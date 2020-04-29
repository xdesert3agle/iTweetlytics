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
        for ($i = 0; $i < 50; $i++) {
            $local = $i;
        }

        dd($local);
    }
}
