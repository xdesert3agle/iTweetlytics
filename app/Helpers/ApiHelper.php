<?php

namespace App\Helpers;

use Thujohn\Twitter\Facades\Twitter;

class ApiHelper {
    public static function reconfig($profile) {
        Twitter::reconfig([
            "token" => decrypt($profile->oauth_token),
            "secret" => decrypt($profile->oauth_token_secret)
        ]);
    }
}
