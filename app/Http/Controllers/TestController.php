<?php

namespace App\Http\Controllers;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Helpers\ApiHelper;
use App\TwitterProfile;
use App\Unfollow;
use App\Unfriend;
use App\UserProfile;
use App\UserProfileTaggedProfiles;
use Carbon\Carbon;
use Thujohn\Twitter\Facades\Twitter;


class TestController extends Controller {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 900;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;
    const MAX_USERS_TAKE_AMOUNT = 1000;

    public function test() {
        $startTime = microtime(true);
        //dd(UserProfile::where('id', 1)->first());

        ApiHelper::reconfig(UserProfile::where('id', 1)->first());
        $test = ApiHelper::getRateLimit('followers');

        $goingToUpdate = ["1184522711432863749","1908468554","4188984616","3254447975","3429511017","999207379827875840","2985062835","1034878662804291585","458484717","394040494","2246988194","1565410238","4349396415","2962690182","286561116","721849189538406401"];

        Follower::where('user_profile_id', 1)
            ->whereIn('twitter_profile_id', $goingToUpdate)
            ->update(['is_present' => true]);

        dd($test);

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
    }
}
