<?php

namespace App\Http\Controllers;

use App\Follow;
use App\Follower;
use App\Friend;
use App\Helpers\ApiHelper;
use App\Report;
use App\TwitterProfile;
use App\Unfollow;
use App\Unfriend;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thujohn\Twitter\Facades\Twitter;
class AppController extends Controller {
    public function index($selectedProfileIndex) {
        $startTime = microtime(true);

        $user = UserController::get($selectedProfileIndex);
        $user->profile_index = $selectedProfileIndex;

        // Se reconfigura la API para realizar las peticiones con el perfil activo
        ApiHelper::reconfig($user->current_twitter_profile[0]);

        // Se realizan las peticiones de la timeline, las menciones, los dms y las listas del perfil
        $timeline = Twitter::getHomeTimeline(['count' => 40, 'tweet_mode' => 'extended', 'format' => 'json']);
        $mentions = Twitter::getMentionsTimeline(['tweet_mode' => 'extended', 'format' => 'json']);
        $chats = $this->getParsedChats();
        $lists = Twitter::getLists(['format' => 'json']);

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        return view('app')->with([
            'timeline' => $timeline,
            'mentions' => $mentions,
            'chats' => $chats,
            'lists' => $lists,
            'user' => $user,
            'loadTime' => $loadTime
        ]);
    }

    public function getParsedFollows($profile) {
        $yearAgo = Carbon::now()->subWeek()->startOfDay();
        $monthAgo = Carbon::now()->subWeek()->startOfDay();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $now = Carbon::now();

        $follows['weekly'] = Follow::whereBetween('created_at', [$weekAgo, $now])
            ->where('twitter_profile_id', $profile->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        $follows['monthly'] = Follow::whereBetween('created_at', [$monthAgo, $now])
            ->where('twitter_profile_id', $profile->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        $follows['yearly'] = Follow::whereBetween('created_at', [$yearAgo, $now])
            ->where('twitter_profile_id', $profile->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        return $follows;
    }

    public function getParsedUnfollows($profile) {
        $yearAgo = Carbon::now()->subWeek()->startOfDay();
        $monthAgo = Carbon::now()->subWeek()->startOfDay();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $now = Carbon::now();

        $unfollows['weekly'] = Unfollow::whereBetween('created_at', [$weekAgo, $now])
            ->where('twitter_profile_id', $profile->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        $unfollows['monthly'] = Unfollow::whereBetween('created_at', [$monthAgo, $now])
            ->where('twitter_profile_id', $profile->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        $unfollows['yearly'] = Unfollow::whereBetween('created_at', [$yearAgo, $now])
            ->where('twitter_profile_id', $profile->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        return $unfollows;
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

    public function unfollowUser(Request $r) {
        $response = Twitter::postUnfollow(['screen_name' => $r->screen_name]);

        if ($response) {
            $friend = Friend::where([
                ['twitter_profile_id', $r->twitter_profile_id],
                ['screen_name', $r->screen_name]
            ])->first();

            $friend->hidden = true;
            $friend->save();

            return [
                'status' => 'success',
                'message' => 'Has dejado de seguir a @' . $r->screen_name . '.'
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Ha ocurrido un error al intentar dejar de seguir a @' . $r->screen_name . '.'
            ];
        }
    }
}
