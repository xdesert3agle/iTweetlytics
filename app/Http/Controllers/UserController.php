<?php

namespace App\Http\Controllers;

use App\TwitterProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;

class UserController extends Controller {
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
}
