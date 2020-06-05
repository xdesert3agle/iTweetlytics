<?php

namespace App\Helpers;

use Carbon\Carbon;
use Thujohn\Twitter\Facades\Twitter;

class ApiHelper {
    public static function reconfig($profile) {
        Twitter::reconfig([
            "token" => decrypt($profile->oauth_token),
            "secret" => decrypt($profile->oauth_token_secret)
        ]);
    }

    public static function getRateLimit($endpoint, $field = null) {
        switch ($endpoint) {
            case 'followers':
                $endpoint_field = "/followers/ids";
                break;

            case 'friends':
                $endpoint_field = "/friends/ids";
                break;

            case 'users':
                $endpoint_field = "/users/lookup";
                break;
        }

        $rate_limit = Twitter::getAppRateLimit(['format' => 'array'])['resources'][$endpoint][$endpoint_field];

        return $field
            ? ($field != 'reset'
                ? $rate_limit[$field]
                : Carbon::createFromTimestamp($rate_limit[$field])->diffInSeconds())
            : $rate_limit;
    }
}
