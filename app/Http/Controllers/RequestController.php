<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller {
    public function getHomeTimeline() {
        return Twitter::getHomeTimeline(['count' => 25, 'tweet_mode' => 'extended', 'format' => 'json']);
    }
}
