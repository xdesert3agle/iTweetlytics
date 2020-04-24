<?php

namespace App\Helpers;

use Thujohn\Twitter\Facades\Twitter;

class UtilHelper {
    public static function array_diff_hashmap($a, $b) {
        $map = array_flip($a);
        foreach($b as $val) unset($map[$val]);
        return array_flip($map);
    }
}
