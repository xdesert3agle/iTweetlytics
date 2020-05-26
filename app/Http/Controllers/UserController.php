<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Helpers\ApiHelper;
use App\SyncedProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thujohn\Twitter\Facades\Twitter;

class UserController extends Controller {
    public static function get($profileIndex) {
        $user = User::where('id', Auth::id())
            ->with('synced_profiles')
            ->with(['current_synced_profile' => function ($query) use ($profileIndex) {
                $query->with('followers')
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
                    ->with('tags')
                    ->skip($profileIndex)->take(1)->first();
            }])
            ->first();

        $aux = $user->current_synced_profile[0];
        unset($user->current_synced_profile);
        $user->current_synced_profile = $aux;

        return $user;
    }

    public function refresh($profileId) {
        $user = Auth::user();
        $profile = SyncedProfile::find($profileId);

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
