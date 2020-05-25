<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Helpers\UtilHelper;
use App\ProfilesUrls;
use App\TwitterProfile;
use Thujohn\Twitter\Facades\Twitter;


class TestController extends Controller {
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    public function test() {
        $profile = TwitterProfile::find(286561116);
        $user = Twitter::getUsersLookup(['screen_name' => 'Elmiillor', 'format' => 'object']);
        $user = $user[0];

        foreach ($user->entities as $urlPlace) {
            foreach ($urlPlace->urls as $url) {
                echo $url->expanded_url;
            }
        }
    }
}
