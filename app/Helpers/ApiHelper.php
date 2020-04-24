<?php

namespace App\Helpers;

use Thujohn\Twitter\Facades\Twitter;

class ApiHelper {
    public static function reconfig($profile) {
        Twitter::reconfig([
            "token" => $profile->oauth_token,
            "secret" => $profile->oauth_token_secret,
        ]);
    }
}
