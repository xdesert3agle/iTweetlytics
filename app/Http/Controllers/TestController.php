<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    function test() {
        //$tl = Twitter::getUserTimeline(['screen_name' => 'miguel_vb17', 'count' => 20, 'format' => 'json']);

        $file = Storage::get('tl.json');
        $tl = json_decode(json_encode($file));

        return view('home')->with([
            'tl' => $tl,
        ]);
    }
}
