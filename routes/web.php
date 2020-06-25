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

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'sync.completed'], function() {
        Route::prefix('ajax')->group(function () {
            Route::prefix('user')->group(function () {
                Route::get('refresh/{profileId}', 'UserController@refresh');
                Route::post('profile/change', 'UserController@changeSelectedProfile');

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

                Route::post('sendReply', 'AppController@sendReply');
            });

            Route::prefix('list')->group(function () {
                Route::get('fetch', 'ListController@fetchListTweets');
            });

            Route::prefix('profile')->group(function () {
                Route::prefix('{profileId}')->group(function () {
                    Route::prefix('stats')->group(function () {
                        Route::prefix('tags')->group(function () {
                            Route::get('{target}', 'StatsController@getTagsData');
                        });
                        Route::get('{stat}/{timeInterval}', 'StatsController@getStat');
                    });
                });

                Route::post('unsync', 'AppController@unsyncProfile');

                Route::post('scheduled_tweet/delete', 'AppController@deleteScheduledTweet');

                Route::prefix('tags')->group(function () {
                    Route::post('add', 'TagsController@addTag');
                    Route::post('delete', 'TagsController@deleteTag');

                    Route::prefix('words')->group(function () {
                        Route::post('update', 'TagsController@updateWords');
                    });
                    Route::prefix('regexes')->group(function () {
                        Route::post('update', 'TagsController@updateRegexes');
                    });
                });

                Route::post('unfollow', 'AppController@unfollowUser');
                Route::post('follow', 'AppController@followUser');
            });
        });

        Route::get('app', 'AppController@index')->middleware('auth')->name('app');
        Route::get('app/{extra}', function () {
            return redirect('app');
        });
    });

    Route::get('sync', 'SyncController@index')->name('profile.sync')->middleware('sync.incomplete');

    Route::prefix('twitter')->group(function () {
        Route::get('login', 'LoginWithTwitterController@requestLogin')->name('twitter.login');
        Route::get('callback', 'LoginWithTwitterController@getTwitterCallback')->name('twitter.callback');
        Route::get('error', 'LoginWithTwitterController@error')->name('twitter.error');
        Route::get('logout', 'LoginWithTwitterController@logout')->name('twitter.logout');
    });
});

Route::get('about', function () {
    return view('about');
})->name('about');

Route::get('privacy', function () {
    return view('privacy');
})->name('privacy');

Auth::routes();
