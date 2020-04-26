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
        return User::find(Auth::id())
            ->with('twitter_profiles')
            ->with(['current_twitter_profile' => function ($query) use ($profileIndex) {
                $query->with('followers')
                    ->with('reports')
                    ->with('follows')
                    ->with('unfollows')
                    ->orderBy('created_at')
                    ->skip($profileIndex)->take(1);
            }])
            ->first();
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
