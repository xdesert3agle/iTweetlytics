<?php

namespace App\Helpers;

use Exception;
use Thujohn\Twitter\Facades\Twitter;

class UtilHelper {
    public static function array_diff_hashmap($a, $b) {
        $map = array_flip($a);
        foreach ($b as $val) unset($map[$val]);
        return array_flip($map);
    }

    public static function expandShortUrl($url) {
        $headers = get_headers($url, 1);
        $location = $headers['Location'];

        if (is_array($location)) {
            $key = max(array_keys($location));
            return $location[$key];
        } else {
            return $location;
        }
    }
}
