<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Helpers\ApiHelper;
use App\Jobs\ScheduledTweetJob;
use App\ScheduledTweet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Thujohn\Twitter\Facades\Twitter;

class AppController extends Controller {
    public function index() {
        $startTime = microtime(true);

        $user = UserController::get();

        // Se reconfigura la API para realizar las peticiones con el perfil activo
        ApiHelper::reconfig($user->current_user_profile);

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

    public function postTweet(Request $r) {
        if (!$r->scheduleTime) {
            Twitter::postTweet(['status' => $r->text]);

            return [
                'status' => 'success',
                'message' => 'El tweet ha sido publicado.'
            ];
        } else {
            $scheduledTweet = ScheduledTweet::create([
                'user_profile_id' => $r->user_profile_id,
                'tweet_content' => $r->text,
                'schedule_time' => $r->scheduleTime,
                'status' => 'queued'
            ]);

            $now = Carbon::createFromTimestamp($r->now);
            $target_date = Carbon::createFromTimestamp($r->scheduleTime / 1000);

            $seconds_until_date = $now->diffInSeconds($target_date);

            ScheduledTweetJob::dispatch($scheduledTweet)->delay(now()->addSeconds($seconds_until_date));

            return [
                'status' => 'success',
                'message' => 'El tweet ha sido programado con éxito.'
            ];
        }
    }

    public function deleteScheduledTweet(Request $r) {
        ScheduledTweet::destroy($r->tweetId);
    }

    function getParsedChats() {
        $dms = Twitter::getDms(['count' => 50]);

        if (!empty($dms->events)) {
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
        } else {
            return [];
        }
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
                ['user_profile_id', $r->user_profile_id],
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

    public function followUser(Request $r) {
        $response = Twitter::postFollow(['screen_name' => $r->screen_name]);

        if ($response) {
            return [
                'status' => 'success',
                'message' => 'Has comenzado a seguir a @' . $r->screen_name . '.'
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Ha ocurrido un error al intentar seguir a @' . $r->screen_name . '.'
            ];
        }
    }

    public function sendReply(Request $r) {
        if (strpos($r->tweet_content, '@' . $r->target_screen_name) !== true) {
            $r->tweet_content = $r->tweet_content . ' @' . $r->target_screen_name;
        }

        $result = Twitter::postTweet(['status' => $r->tweet_content, 'in_reply_to_status_id' => $r->target_id]);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Tu respuesta ha sido enviada.'
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Ha ocurrido un error al intentar enviar tu respuesta.'
            ];
        }
    }
}
