<?php

namespace App\Http\Controllers;

use App\Follower;
use App\Jobs\UpdateFollowersList;
use App\TwitterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    function test() {
        dd(Twitter::getAppRateLimit());
        $profile = Twitter::getUsersLookup(['screen_name' => 'earcos']);

        // Se calcula en número de peticiones necesarias para poder fetchear la lista completa de followers
        $neededJobs = $profile[0]->followers_count / (self::MAX_CONSECUTIVE_REQUESTS * self::FOLLOWERS_PER_REQUEST);

        //for ($i = 0; $i < 1; $i++) {
            $cursor = -1;
            $count = 0;

            do {
                $response = Twitter::getFollowersIds(['screen_name' => 'earcos', 'cursor' => $cursor, 'count' => 5000]);
                $cursor = $response->next_cursor;

                $file = Storage::get('file.txt');
                Storage::put('file.txt', $file . "Llamada $count\n");

            } while (++$count < self::MAX_CONSECUTIVE_REQUESTS && $cursor != 0);
        //}

        /*

        // Se programan los jobs necesarios, con suficiente espacio entre ellos para no llegar al Rate Limit
        for ($i = 0; $i < ceil($neededJobs); $i++) {
            //$cursor = $this->twitterProfile->next_followers_cursor;
            $cursor = -1;

            if ($cursor == 0)
                $cursor = -1;

            $count = 0;

            Twitter::reconfig([
                "token" => $this->twitterProfile->oauth_token,
                "secret" => $this->twitterProfile->oauth_token_secret,
            ]);

            $followers = [];

            do {
                $response = Twitter::getFollowersIds(['screen_name' => 'earcos', 'cursor' => $cursor]);
                $cursor = $response->next_cursor;

                foreach ($response->ids as $id) {
                    $follower = new Follower;
                    $follower->twitter_profiles_id = $this->twitterProfile->id;
                    $follower->twitter_user_id = $id;
                    $follower->save();



                    $followers[$count][] = $id;
                    $file = Storage::get('file.txt');
                    Storage::put('file.txt', $file . "Llamada $count");
                }
            } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS);

            dd($followers);
        }*/
    }
}
