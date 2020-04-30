<?php

namespace App\Http\Controllers;

use App\Follower;
use App\TwitterProfile;

class TestController extends Controller {
    public function test() {
        dd(Follower::all()->keyBy('id_str')->toArray());
    }
}
