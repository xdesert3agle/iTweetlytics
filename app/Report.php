<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model {
    public static function generateDailyReport($profile) {
        $follows_count = Follow::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $unfollows_count = Unfollow::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $total_followers_count = Follower::where('twitter_profile_id', $profile->id)
            ->count();

        $befriends_count = Befriend::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $unfriends_count = Unfriend::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $total_friends_count = Friend::where('twitter_profile_id', $profile->id)
            ->count();

        $report = new Report;
        $report->twitter_profile_id = $profile->id;
        $report->follows = $follows_count;
        $report->unfollows = $unfollows_count;
        $report->followers_variation = $follows_count - $unfollows_count;
        $report->total_followers = $total_followers_count;
        $report->befriends = $befriends_count;
        $report->unfriends = $unfriends_count;
        $report->total_friends = $total_friends_count;
        $report->followback_percent = self::calcFollowbackPercentage($profile);

        $report->save();
    }

    public static function calcFollowbackPercentage($profile) {
        $all_friends_count = Friend::where('twitter_profile_id', $profile->id)->count();

        $users_following_count = Friend::where([
            ['twitter_profile_id', $profile->id],
            ['follows_you', true]
        ])->count();

        return ($users_following_count / $all_friends_count) * 100;
    }
}
