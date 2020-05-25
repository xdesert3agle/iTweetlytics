<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Helpers\UtilHelper;
use App\TwitterProfile;
use App\Url;
use App\SyncedProfile;
use Illuminate\Support\Facades\Hash;
use Thujohn\Twitter\Facades\Twitter;


class TestController extends Controller {
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    public function test() {
        $elm = Twitter::getUsersLookup(['screen_name' => 'elmiillor']);
        $elm = $elm[0];

        TwitterProfile::insertIfNew($elm);
    }
}
