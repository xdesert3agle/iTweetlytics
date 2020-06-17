<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\TwitterProfile;
use App\UserProfile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;

class LoginWithTwitterController extends Controller {
    const TWITTER_ERROR = 1;
    const PROFILE_ALREADY_LINKED = 2;

    public function requestLogin() {
        $this->logoutWithoutRedirect();

        // your SIGN IN WITH TWITTER button should point to this route
        $sign_in_twitter = true;
        $force_login = true;

        // Make sure we make this request w/o tokens, overwrite the default values in case of login.
        Twitter::reconfig(['token' => '', 'secret' => '']);
        $token = Twitter::getRequestToken(route('twitter.callback'));

        if (isset($token['oauth_token_secret'])) {
            $url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

            Session::put('oauth_state', 'start');
            Session::put('oauth_request_token', $token['oauth_token']);
            Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

            return Redirect::to($url);
        }

        return Redirect::route('twitter.error');
    }

    public function getTwitterCallback() {
        if (Session::has('oauth_request_token')) {
            $request_token = [
                'token' => Session::get('oauth_request_token'),
                'secret' => Session::get('oauth_request_token_secret'),
            ];

            Twitter::reconfig($request_token);

            $oauth_verifier = false;

            if (request()->has('oauth_verifier')) {
                $oauth_verifier = request()->get('oauth_verifier');
                $tokens = Twitter::getAccessToken($oauth_verifier);
            }

            if (!isset($tokens['oauth_token_secret'])) {
                return Redirect::route('twitter.error')->with('flash_error', 'We could not log you in on Twitter.');
            }

            $credentials = Twitter::getCredentials();

            if (is_object($credentials) && !isset($credentials->error)) {
                Session::put('access_token', $tokens);

                // El perfil de Twitter no debe haberse sincronizado por alguien más
                if (!UserProfile::linkedByOtherUser($credentials)) {
                    $new_twitter_profile = TwitterProfile::firstOrCreate(
                        ['id' => $credentials->id_str],
                        (array) $credentials
                    );

                    UserProfile::linkToUser($new_twitter_profile, $tokens);

                    // Se comineza el procesamiento de datos del perfil (seguidores, seguidos...)
                    Artisan::call('profile:process', [
                        'target' => $credentials->id_str
                    ]);
                } else {
                    return Redirect::route('twitter.error');
                }

                return Redirect::to(RouteServiceProvider::APP)->with('error', self::PROFILE_ALREADY_LINKED);
            }

            return Redirect::route('twitter.error');
        }
    }

    public function error() {
        return view('sync_error');
    }

    public function logout() {
        Session::forget('access_token');

        return Redirect::to('/');
    }

    public function logoutWithoutRedirect() {
        Session::forget('access_token');
    }
}
