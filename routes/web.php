<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'LandingPageController@index')->middleware('guest');
Route::get('test', 'TestController@test')->middleware('guest');

// Rutas para usuarios autenticados
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('ajax')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('refresh/{profileId}', 'UserController@refresh');

            Route::get('get', 'UserController@getFromRequest');
        });

        Route::prefix('dm')->group(function () {
            Route::post('send', 'UserController@sendDm');
        });

        Route::prefix('tweets')->group(function () {
            Route::post('/new', 'AppController@postTweet');

            Route::prefix('retweet')->group(function () {
                Route::post('/', 'AppController@retweetTweet');
                Route::post('remove', 'AppController@removeRetweet');
            });

            Route::prefix('favorite')->group(function () {
                Route::post('/', 'AppController@favoriteTweet');
                Route::post('remove', 'AppController@removeFavorite');
            });
        });

        Route::prefix('list')->group(function () {
                Route::get('fetch', 'ListController@fetchListTweets');
        });

        Route::prefix('profile')->group(function () {
            Route::prefix('{profileId}/stats')->group(function () {
                Route::get('{stat}/{timeInterval}', 'StatsController@getReportStat');
            });
        });
    });

    Route::get('app/{selectedProfileIndex}', 'AppController@index')->middleware('auth')->name('app');
    Route::get('app', function () {
        return redirect('app/0');
    })->middleware('auth');
});

Route::get('twitter/login', ['as' => 'twitter.login', 'uses' => 'LoginWithTwitterController@requestLogin']);

Route::get('twitter/callback', ['as' => 'twitter.callback', 'uses' => 'LoginWithTwitterController@getTwitterCallback']);

Route::get('twitter/error', ['as' => 'twitter.error', 'uses' => 'LoginWithTwitterController@error']);

Route::get('twitter/logout', ['as' => 'twitter.logout', 'uses' => 'LoginWithTwitterController@logout']);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
