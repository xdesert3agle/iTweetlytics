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

Route::get('/', 'LandingPageController@index');

Route::get('twitter/login', ['as' => 'twitter.login', 'uses' => 'LoginWithTwitterController@requestLogin']);

Route::get('twitter/callback', ['as' => 'twitter.callback', 'uses' => 'LoginWithTwitterController@getTwitterCallback']);

Route::get('twitter/error', ['as' => 'twitter.error', 'uses' => 'LoginWithTwitterController@error']);

Route::get('twitter/logout', ['as' => 'twitter.logout', 'uses' => 'LoginWithTwitterController@logout']);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
