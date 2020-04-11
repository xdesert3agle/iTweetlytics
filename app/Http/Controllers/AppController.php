<?php

namespace App\Http\Controllers;

use App\TwitterProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thujohn\Twitter\Facades\Twitter;

class AppController extends Controller {
    public function index() {
        $timeline = Twitter::getHomeTimeline(['count' => 30, 'tweet_mode' => 'extended', 'format' => 'json']);
        $mentions = Twitter::getMentionsTimeline(['tweet_mode' => 'extended', 'format' => 'json']);
        $chats = $this->getParsedChats();
        $user = User::with('twitter_profiles')->find(Auth::id());

        return view('app')->with([
            'user' => $user,
            'timeline' => $timeline,
            'mentions' => $mentions,
            'chats' => $chats
        ]);
    }

    function getParsedChats() {
        $dms = Twitter::getDms();
        $messages = [];
        $myId = "";

        foreach ($dms->events as $dm) {
            if (property_exists($dm->message_create, 'source_app_id')) {
                $myId = $dm->message_create->sender_id;
                break;
            }
        }

        foreach ($dms->events as $dm) {
            if ($dm->message_create->sender_id != $myId) {
                $personId = $dm->message_create->sender_id;
            } else {
                $personId = $dm->message_create->target->recipient_id;
            }

            $messages[$personId][] = $dm;
        }

        $userProfiles = Twitter::getUsersLookup(['user_id' => array_keys($messages)]);

        $chats = [];
        foreach ($userProfiles as $i => $user) {
            $chats[$i]['user'] = $user;
            $chats[$i]['messages'] = $messages[$user->id];
        }

        return json_encode($chats);
    }

    public function retweetTweet(Request $r) {
        return Twitter::postRt($r->id, ['tweet_mode' => 'extended', 'format' => 'json']);
    }

    public function favoriteTweet(Request $r) {
        return Twitter::postFavorite(['id' => $r->id, 'tweet_mode' => 'extended', 'format' => 'json']);
    }

    public function removeRetweet(Request $r) {
        $userTl = Twitter::getUserTimeline();

        foreach ($userTl as $tweet) {
            if ($tweet->retweeted) {
                if ($tweet->retweeted_status->id_str == $r->id) {
                    $removedTweet = Twitter::destroyTweet($tweet->id_str, ['tweet_mode' => 'extended']);
                    $removedTweet->retweeted_status->retweeted = false;
                    return json_encode($removedTweet->retweeted_status);
                }
            }
        }
    }

    public function removeFavorite(Request $r) {
        return Twitter::destroyFavorite(['id' => $r->id, 'tweet_mode' => 'extended', 'format' => 'json']);
    }
}
