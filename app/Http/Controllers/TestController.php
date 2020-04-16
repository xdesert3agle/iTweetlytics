<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    function test() {
        $cursor = -1;
        $api_path = "https://api.twitter.com/1.1/endpoint.json?screen_name=targetUser";

        $followers = [];
        $count = 0;

        Twitter::reconfig([
            "token" => '722857467495854080-j5nsTXCs7OmQkQ80uNyNEaOV5nKzSQ9',
            "secret" => 'VG7SzJed3TVtUj4U0uOtIUMj34cw8JVNF2bv5zI66a9VH',
        ]);

        do {
            $response = Twitter::getFollowersIds(['screen_name' => 'natiroman9', 'cursor' => $cursor]);
            $followers[] = $response;
            $cursor = $response->next_cursor;
        } while ($cursor != 0 && ++$count <= 2);

        dd($followers);
    }
}
