<?php

namespace App\Http\Controllers;

use App\UserProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Async\Pool;
use Thujohn\Twitter\Facades\Twitter;

class ListController extends Controller {
    public function fetchListTweets(Request $r) {
        return Twitter::getListStatuses(['list_id' => $r->id_str, 'tweet_mode' => 'extended', 'format' => 'json']);
    }
}
