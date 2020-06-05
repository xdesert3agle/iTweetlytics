<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Helpers\ApiHelper;
use App\UserProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thujohn\Twitter\Facades\Twitter;

class UserController extends Controller {
    public static function get($profileIndex) {
        $startTime = microtime(true);

        $user = User::where('id', Auth::id())
            ->with('user_profiles')
            ->with(['current_user_profile' => function ($query) use ($profileIndex) {
                $query->with(['followers' => function ($query) {
                    $query->limit(500);
                }])
                    ->with(['follows' => function ($query) {
                        $query->limit(500);
                    }])
                    ->with(['unfollows' => function ($query) {
                        $query->limit(500);
                    }])
                    ->with(['friends' => function ($query) {
                        $query->where('hidden', false);
                    }])
                    ->with(['befriends' => function ($query) {
                        $query->limit(500);
                    }])
                    ->with(['unfriends' => function ($query) {
                        $query->limit(500);
                    }])
                    ->with('reports')
                    ->with(['scheduled_tweets' => function ($query) {
                        $query->where('status', '!=', 'sent')
                            ->get();
                    }])
                    ->with('tags')
                    ->skip($profileIndex)->take(1);
            }])
            ->first();

        $aux = $user->current_user_profile[0];
        unset($user->current_user_profile);
        $user->current_user_profile = $aux;

        $followers = $user->current_user_profile->followers->mapWithKeys(function ($item) {
            return [$item['id'] => $item];
        });

        unset($user->current_user_profile->followers);
        $user->current_user_profile->followers = $followers;

        $friends = $user->current_user_profile->friends->mapWithKeys(function ($item) {
            return [$item['id'] => $item];
        });
        unset($user->current_user_profile->friends);
        $user->current_user_profile->friends = $friends;

        return $user;
    }

    public static function memory_usage() {
        $mem_usage = memory_get_usage(true);
        if ($mem_usage < 1024) {
            $mem_usage .= ' bytes';
        } elseif ($mem_usage < 1048576) {
            $mem_usage = round($mem_usage / 1024, 2) . ' kilobytes';
        } else {
            $mem_usage = round($mem_usage / 1048576, 2) . ' megabytes';
        }
        echo($mem_usage);
    }

    public function refresh($profileId) {
        $user = Auth::user();
        $profile = UserProfile::find($profileId);

        if ($profile->belongsToUser($user->id) && $profile->canBeRefreshed()) {
            ApiHelper::reconfig($profile);

            $profile->fill(collect(Twitter::getCredentials())->toArray());
            $profile->save();

            return [
                'status' => 'success',
                'message' => 'El perfil @' . $profile->screen_name . ' ha sido actualizado',
                'data' => $profile
            ];
        } else {
            return [
                'status' => 'error',
                'error' => 'Not enough time',
                'message' => 'Por favor, espera ' . $profile->secsUntilRefresh() . ' segundos para poder refrescar este perfil de nuevo.'
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
