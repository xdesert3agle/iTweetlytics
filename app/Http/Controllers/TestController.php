<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {

    public function test() {
        dd(Carbon::today()->subDay()->toDateString());
    }
}
