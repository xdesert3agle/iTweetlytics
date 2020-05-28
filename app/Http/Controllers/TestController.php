<?php

namespace App\Http\Controllers;

class TestController extends Controller {
    public function test() {
        $regexes = "//";
        dd(preg_match_all($regexes, 'mis huevos 33'));
    }
}
