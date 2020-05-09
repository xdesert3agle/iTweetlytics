<?php

namespace App\Http\Controllers;

use App\Follower;

class TestController extends Controller {

    public function test() {
        $class = Follower::class;
        dd($class::all());
    }
}
