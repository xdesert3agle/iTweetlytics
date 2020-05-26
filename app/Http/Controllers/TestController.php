<?php

namespace App\Http\Controllers;

use App\Follower;

class TestController extends Controller {
    public function test() {
        $users_list = Follower::where('synced_profile_id', 286561116)
            ->with('twitter_profile')
            ->get();

        dd($users_list->toArray());
    }
}
