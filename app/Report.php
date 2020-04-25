<?php

namespace App;

use App\Helpers\UtilHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class Report extends Model {
    public static function generateDailyReport($profile, $dbFollowers) {
        $follows = Follow::where([
            ['twitter_profile_id', $profile->id],
        ])->whereDate('created_at', Carbon::today())->get();

        $unfollows = Unfollow::where([
            ['twitter_profile_id', $profile->id],
        ])->whereDate('created_at', Carbon::today())->get();

        $report = new Report;
        $report->twitter_profile_id = $profile->id;
        $report->follows = count($follows);
        $report->unfollows = count($unfollows);
        $report->followers_variation = $report->follows - $report->unfollows;
        $report->profile_total_followers = Follower::where('twitter_profile_id', $profile->id)->get()->count();
        $report->followback_percent = self::calcFollowbackPercentage($profile, $dbFollowers);

        $report->save();
    }

    public static function calcFollowbackPercentage($profile, $dbFollowers) {
        $friendsIds = Friend::where('twitter_profile_id', $profile->id)->get()->pluck('id_str')->toArray();

        $usersNotFollowingCount = count(array_diff($friendsIds, $dbFollowers));

        return 100 - ($usersNotFollowingCount / count($friendsIds)) * 100;
    }

    public static function addToLog($string) {
        $file = Storage::get('log.txt');
        Storage::put('log.txt', $file . "\n$string");
    }
}
