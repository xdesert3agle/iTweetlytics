<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;

class LoginWithTwitterController extends Controller {
    public function requestLogin() {
        // your SIGN IN WITH TWITTER button should point to this route
        $sign_in_twitter = true;
        $force_login = false;

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

                $user = $this->handleUserLogin($credentials, $tokens);
                Auth::login($user);

                return Redirect::to('/');
            }

            return Redirect::route('twitter.error');
        }
    }

    public function error() {
        // Something went wrong, add your own error handling here
    }

    public function logout() {
        Session::forget('access_token');
        Auth::logout();

        return Redirect::to('/');
    }

    public function handleUserLogin($credentials, $tokens) {
        // Si el usuario que es está haciendo login ya existe en nuestra BBDD => Se recupera
        // Si no existe en nuestra BBDD => Primero se inserta y luego se recupera
        return User::firstOrCreate(
            ['id' => $credentials->id_str],
            [
                'id' => $credentials->id,
                'name'  => $credentials->name,
                'screen_name'  => $credentials->screen_name,
                'location' => $credentials->location,
                'description' => $credentials->description,
                'protected' => $credentials->protected,
                'followers_count' => $credentials->followers_count,
                'friends_count' => $credentials->friends_count,
                'listed_count' => $credentials->listed_count,
                'favourites_count' => $credentials->favourites_count,
                'time_zone' => $credentials->time_zone,
                'geo_enabled' => $credentials->geo_enabled,
                'verified' => $credentials->verified,
                'statuses_count' => $credentials->statuses_count,
                'profile_background_color' => $credentials->profile_background_color,
                'profile_image_url' => $credentials->profile_image_url,
                'profile_banner_url' => $credentials->profile_banner_url,
                'profile_link_color' => $credentials->profile_link_color,
                'lang' => $credentials->lang,
                'suspended' => $credentials->suspended,
                'oauth_token' => $tokens['oauth_token'],
                'oauth_token_secret' => $tokens['oauth_token_secret']
            ]
        );
    }
}
