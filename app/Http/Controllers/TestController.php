<?php

namespace App\Http\Controllers;

use App\TwitterProfile;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {

    public function test() {
        dd(Twitter::getFollowersIds(['screen_name' => 'virtu_callosa', 'cursor' => -1, 'count' => 5000, 'stringify_ids' => 'true']));

        /*Artisan::call('profile:process', [
            'target' => 1172641896
        ]);*/
    }
}
