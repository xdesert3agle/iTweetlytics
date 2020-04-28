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

        $all_followers = Follower::where('twitter_profile_id', $profile->id)->get();
        $all_followers_ids =$all_followers->pluck('id_str')->toArray();
        $total_followers_count = count($all_followers);

        $befriends_count = Befriend::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $unfriends_count = Unfriend::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $all_friends = Friend::where('twitter_profile_id', $profile->id)->get();
        $all_friends_ids = $all_friends->pluck('id_str')->toArray();
        $total_friends_count = count($all_friends);

        $report = new Report;
        $report->twitter_profile_id = $profile->id;
        $report->follows = $follows_count;
        $report->unfollows = $unfollows_count;
        $report->followers_variation = $follows_count - $unfollows_count;
        $report->total_followers = $total_followers_count;
        $report->befriends = $befriends_count;
        $report->unfriends = $unfriends_count;
        $report->total_friends = $total_friends_count;
        $report->followers_followback_percent = self::calcFollowbackPercentage($profile, count($all_friends));
        $report->user_followback_percent = self::calcUserFollowbackPercentage($all_followers_ids, $all_friends_ids);

        $report->save();
    }

    public static function calcFollowbackPercentage($profile, $all_friends_count) {
        $all_friends_count = Friend::where('twitter_profile_id', $profile->id)->count();

        $users_following_count = Friend::where([
            ['twitter_profile_id', $profile->id],
            ['follows_you', true]
        ])->count();

        return round(($users_following_count / $all_friends_count) * 100, 2);
    }

    public static function calcUserFollowbackPercentage($followers, $friends) {
        $followers_not_following_count = count(array_diff($followers, $friends));
        $all_followers_count = count($followers);

        return round(100 - (($followers_not_following_count / $all_followers_count) * 100), 2);
    }
}
