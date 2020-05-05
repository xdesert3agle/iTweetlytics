<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\TwitterProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thujohn\Twitter\Facades\Twitter;

class UserController extends Controller {
    public static function get($profileIndex) {
        $user = User::find(Auth::id())
            ->with('twitter_profiles')
            ->with(['current_twitter_profile' => function ($query) use ($profileIndex) {
                $query->with(['followers' => function ($query) {
                    $query->orderBy('followers.id')->get();
                }])
                    ->with('follows')
                    ->with('unfollows')
                    ->with(['friends' => function ($query) {
                        $query->where('hidden', false);
                    }])
                    ->with('befriends')
                    ->with('unfriends')
                    ->with('reports')
                    ->with(['scheduled_tweets' => function ($query) {
                        $query->where('status', '!=', 'sent')
                            ->get();
                    }])
                    ->skip($profileIndex)->take(1);
            }])
            ->first();


        if ($user->current_twitter_profile[0]->scheduled_tweets->count() > 0) {
            foreach ($user->current_twitter_profile[0]->scheduled_tweets as $tweet) {
                $formatted_scheduled_tweets[Carbon::createFromTimestamp($tweet->schedule_time / 1000)->format('d/m/Y')][] = $tweet;
            }

            unset($user->current_twitter_profile[0]->scheduled_tweets);
            $user->current_twitter_profile[0]->scheduled_tweets = $formatted_scheduled_tweets;
        }

        $followers = $user->current_twitter_profile[0]->followers->mapWithKeys(function ($item) {
            return [$item['id_str'] => $item];
        });
        unset($user->current_twitter_profile[0]->followers);
        $user->current_twitter_profile[0]->followers = $followers;

        $friends = $user->current_twitter_profile[0]->friends->mapWithKeys(function ($item) {
            return [$item['id_str'] => $item];
        });
        unset($user->current_twitter_profile[0]->friends);
        $user->current_twitter_profile[0]->friends = $friends;

        return $user;
    }

    public static function getFromRequest(Request $r) {
        $now = Carbon::now();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $twoWeeksAgo = Carbon::now()->subWeeks(2)->startOfDay();
        $monthAgo = Carbon::now()->subMonth()->startOfDay();
        $yearAgo = Carbon::now()->subYear()->startOfDay();

        switch ($r->timeInterval) {
            case 'weekly':
                $fromTime = $weekAgo;
                break;

            case 'biweekly':
                $fromTime = $twoWeeksAgo;
                break;

            case 'monthly':
                $fromTime = $monthAgo;
                break;

            case 'yearly':
                $fromTime = $yearAgo;
                break;
        }

        $profileIndex = $r->profile_index;

        return User::find(Auth::id())
            ->with('twitter_profiles')
            ->with(['current_twitter_profile' => function ($query) use ($profileIndex, $fromTime, $now) {
                $query->with('followers')
                    ->with(['reports' => function ($query) use ($fromTime, $now) {
                        $query->whereBetween('created_at', [$fromTime, $now]);
                    }])
                    ->with(['follows' => function ($query) use ($fromTime, $now) {
                        $query->whereBetween('created_at', [$fromTime, $now]);
                    }])
                    ->with(['unfollows' => function ($query) use ($fromTime, $now) {
                        $query->whereBetween('created_at', [$fromTime, $now]);
                    }])
                    ->orderBy('created_at')
                    ->skip($profileIndex)->take(1);
            }])
            ->first();
    }

    public function refresh($profileId) {
        $user = Auth::user();

        $twProfile = TwitterProfile::find($profileId);

        if ($twProfile->belongsToUser($user->id) && $twProfile->canBeRefreshed()) {
            Twitter::reconfig([
                "token" => $twProfile->oauth_token,
                "secret" => $twProfile->oauth_token_secret,
            ]);

            $twProfile->fill(collect(Twitter::getCredentials())->toArray());
            $twProfile->save();

            return [
                'status' => 'success',
                'message' => 'El perfil @' . $twProfile->screen_name . ' ha sido actualizado',
                'data' => $twProfile
            ];
        } else {
            return [
                'status' => 'error',
                'error' => 'Not enough time',
                'message' => 'Por favor, espera ' . $twProfile->secsUntilRefresh() . ' segundos para poder refrescar este perfil de nuevo.'
            ];
        }
    }

    public function sendDm(Request $r) {
        $data = [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $r->recipientId
                    ],
                    'message_data' => [
                        'text' => $r->text
                    ]
                ]
            ]
        ];

        $connection = new TwitterOAuth(config('ttwitter.CONSUMER_KEY'), config('ttwitter.CONSUMER_SECRET'), config('ttwitter.ACCESS_TOKEN'), config('ttwitter.ACCESS_TOKEN_SECRET'));
        $result = $connection->post('direct_messages/events/new', $data, true);
        return json_encode($result);
    }
}
