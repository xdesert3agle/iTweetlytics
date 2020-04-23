<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    public static function generateDailyReport($profile) {
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

        $report->save();
    }
}
