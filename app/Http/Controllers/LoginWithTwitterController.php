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
        // your SIGN IN WITH TWITTER  button should point to this route
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

            if (Session::has('oauth_verifier')) {
                $oauth_verifier = Request::get('oauth_verifier');
                // getAccessToken() will reset the token for you
                $token = Twitter::getAccessToken($oauth_verifier);
            }

            if (!isset($token['oauth_token_secret'])) {
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
                Session::put('access_token', $token);

                $user = User::find($credentials->id_str);

                if (!$user) { // Si el usuario no existe se le registra en la bd y se le loguea
                    $newUser = $this->createNewUserFromCredentials($credentials);
                } else { // Si el usuario existe se le loguea
                    Auth::login($user);
                }

                return Redirect::to('/')->with('user', $user);
            }

            return Redirect::route('twitter.error')->with('flash_error', 'Crab! Something went wrong while signing you up!');
        }
    }

    public function error() {
        // Something went wrong, add your own error handling here
    }

    public function logout() {
        Session::forget('access_token');
        return Redirect::to('/')->with('flash_notice', 'You\'ve successfully logged out!');
    }

    public function createNewUserFromCredentials($credentials) {
        $user = new User();

        $user->id = $credentials->id_str;
        $user->name = $credentials->name;
        $user->screen_name = $credentials->screen_name;
        $user->location = $credentials->location;
        $user->description = $credentials->description;
        $user->protected = $credentials->protected;
        $user->followers_count = $credentials->followers_count;
        $user->friends_count = $credentials->friends_count;
        $user->listed_count = $credentials->listed_count;
        $user->favourites_count = $credentials->favourites_count;
        $user->time_zone = $credentials->time_zone;
        $user->verified = $credentials->verified;
        $user->lang = $credentials->lang;
        $user->access_token = $credentials->access_token;

        return $user;
    }
}
