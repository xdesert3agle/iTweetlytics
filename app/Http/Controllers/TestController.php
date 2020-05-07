<?php

namespace App\Http\Controllers;

use App\TwitterProfile;

class TestController extends Controller {

    public function test() {
        $target = TwitterProfile::where('id', 286561116)->get();

        foreach ($target as $i => $profile) {
            dd($profile->toArray());
        }
    }
}
