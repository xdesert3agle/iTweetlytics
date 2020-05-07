<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\TwitterProfile;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;

class LoginWithTwitterController extends Controller {
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
        // You should set this route on your Twitter Application settings as the callback
        // https://apps.twitter.com/app/YOUR-APP-ID/settings
        if (Session::has('oauth_request_token')) {
            $request_token = [
                'token' => Session::get('oauth_request_token'),
                'secret' => Session::get('oauth_request_token_secret'),
            ];

            Twitter::reconfig($request_token);

            $oauth_verifier = false;

            if (request()->has('oauth_verifier')) {
                $oauth_verifier = request()->get('oauth_verifier');
                // getAccessToken() will reset the token for you
                $tokens = Twitter::getAccessToken($oauth_verifier);
            }

            if (!isset($tokens['oauth_token_secret'])) {
                return Redirect::route('twitter.error')->with('flash_error', 'We could not log you in on Twitter.');
            }

            $credentials = Twitter::getCredentials();

            if (is_object($credentials) && !isset($credentials->error)) {
                // $credentials contains the Twitter user object with all the info about the user.
                // Add here your own user logic, store profiles, create new users on your tables...you name it!
                // Typically you'll want to store at least, user id, name and access tokens
                // if you want to be able to call the API on behalf of your users.

                // This is also the moment to log in your users if you're using Laravel's Auth class
                // Auth::login($user) should do the trick.
                Session::put('access_token', $tokens);

                // El perfil de Twitter no debe haberse sincronizado por alguien más
                if (!TwitterProfile::find($credentials->id)) {
                    $newProfile = $this->assignTwitterProfileToUser($credentials, $tokens);

                    Artisan::call('profile:process', [
                        'target' => $credentials->id_str
                    ]);
                }

                return Redirect::to(RouteServiceProvider::APP);
            }

            return Redirect::route('twitter.error');
        }
    }

    public function error() {
        // Something went wrong, add your own error handling here
    }

    public function logout() {
        Session::forget('access_token');

        return Redirect::to('/');
    }

    public function logoutWithoutRedirect() {
        Session::forget('access_token');
    }

    public function assignTwitterProfileToUser($credentials, $tokens) {
        $profile_fields = array_merge((array)$credentials, $tokens);
        $profile_fields['user_id'] = Auth::id();
        return TwitterProfile::create($profile_fields);
    }
}
