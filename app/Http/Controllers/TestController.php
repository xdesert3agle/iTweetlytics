<?php

namespace App\Http\Controllers;

use App\Befriend;
use App\Follower;
use App\Friend;
use App\Unfollow;
use App\Unfriend;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    public function test() {
        $tags = ['youtuber', 'streamer', 'periodismo'];
        $words = [['youtube', 'video'], ['twitch', 'mixer'], ['escribo', 'period', 'blog', 'review', 'report']];

        $followers = Friend::all();

        foreach ($followers as $i => $follower) {
            foreach ($tags as $j => $tag) {
                foreach ($words[$j] as $word) {
                    $contains_word = strpos(strtolower($follower->description), $word) !== false;

                    if ($contains_word) {
                        $found_tags[$follower->screen_name][] = $tag;
                        break;
                    }
                }
            }
        }

        dd($found_tags);
    }

    public function update() {
        $befriends = Unfollow::all();
        $unfriends = Follower::all();

        $followers_lookup = [];

        /*for ($i = 0; $i < 2; $i++) {
            $followers_lookup = array_merge($followers_lookup, Twitter::getUsersLookup(['user_id' => array_slice($followers_ids, $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST, self::USERS_LOOKUP_AMOUNT_PER_REQUEST)]));


        }*/

        foreach($unfriends as $j => $unfriend) {
            foreach ($befriends as $k => $befriend) {
                if ($unfriend->id_str == $befriend->id_str) {
                    $unfriend->description = $befriend->description;
                    $unfriend->save();

                    break;
                }
            }
        }
    }
}
