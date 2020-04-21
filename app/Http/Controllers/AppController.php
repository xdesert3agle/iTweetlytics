<?php

namespace App\Http\Controllers;

use App\Report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thujohn\Twitter\Facades\Twitter;

class AppController extends Controller {
    public function index() {
        //dd(Twitter::getAppRateLimit());
        $starttime = microtime(true);
        $timeline = Twitter::getHomeTimeline(['count' => 40, 'tweet_mode' => 'extended', 'format' => 'json']);
        $mentions = Twitter::getMentionsTimeline(['tweet_mode' => 'extended', 'format' => 'json']);
        $chats = $this->getParsedChats();
        $lists = Twitter::getLists(['format' => 'json']);
        $user = User::find(Auth::id())
            ->with('twitter_profiles.followers')
            ->with('twitter_profiles.profile_changes')
            ->with('twitter_profiles.reports')
            ->first();

        $endtime = microtime(true);
        $loadTime = $endtime - $starttime;

        return view('app')->with([
            'timeline' => $timeline,
            'mentions' => $mentions,
            'chats' => $chats,
            'lists' => $lists,
            'user' => $user,
            'loadTime' => $loadTime
        ]);
    }

    public function postTweet(Request $r) {
        Twitter::postTweet(['status' => $r->text]);

        return [
            'status' => 'success',
            'message' => 'El tweet ha sido publicado.'
        ];
    }

    function getParsedChats() {
        $dms = Twitter::getDms(['count' => 50]);
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

    function getParsedLists() {
        $lists = Twitter::getLists();

        foreach ($lists as $i => $list) {
            $lists[$i]->tweets = Twitter::getListStatuses(['list_id' => $list->id_str, 'tweet_mode' => 'extended']);
        }

        return $lists;
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
