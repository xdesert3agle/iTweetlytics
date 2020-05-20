<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    protected $fillable = ['id', 'twitter_profile_id', 'follows', 'unfollows', 'followers_variation', 'befriends', 'unfriends', 'total_followers', 'total_friends', 'followers_followback_percent', 'user_followback_percent', 'friends_to_followers_ratio', 'report_date', 'created_at', 'updated_at'];

    public static function generateDailyReport($profile) {
        // Followers
        $all_followers = Follower::where('twitter_profile_id', $profile->id)->get();
        $total_followers_count = $all_followers->count();

        $follows_count = Follow::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $unfollows_count = Unfollow::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Friends
        $all_friends = Friend::where('twitter_profile_id', $profile->id)->get();
        $total_friends_count = $all_friends->count();

        $befriends_count = Befriend::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $unfriends_count = Unfriend::where('twitter_profile_id', $profile->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        Report::create([
            'twitter_profile_id' => $profile->id,
            'follows' => $follows_count,
            'unfollows' => $unfollows_count,
            'followers_variation' => $follows_count - $unfollows_count,
            'befriends' => $befriends_count,
            'unfriends' => $unfriends_count,
            'total_followers' => $total_followers_count,
            'total_friends' => $total_friends_count,
            'followers_followback_percent' => self::calcFollowbackPercentage($profile, $total_followers_count),
            'user_followback_percent' => self::calcUserFollowbackPercentage($profile, $total_friends_count),
            'friends_to_followers_ratio' => self::calcFriendsToFollowersRatio($total_friends_count, $total_followers_count),
            'report_date' => Carbon::today()->subDay()->toDateString(),
        ]);
    }

    public static function calcFollowbackPercentage($profile, $total_friends_count) {
        $users_following_count = Friend::where('twitter_profile_id', $profile->id)
            ->whereIn('id_str', Follower::where('twitter_profile_id', $profile->id)->get()->pluck('id_str'))
            ->count();

        return round(($users_following_count / $total_friends_count) * 100, 2);
    }

    public static function calcUserFollowbackPercentage($profile, $total_followers_count) {

        $followers_following_back = Follower::where('twitter_profile_id', $profile->id)
            ->whereIn('id_str', Friend::where('twitter_profile_id', $profile->id)->get()->pluck('id_str'))
            ->count();

        return round(($followers_following_back / $total_followers_count) * 100, 2);
    }

    public static function calcFriendsToFollowersRatio($total_friends_count, $total_followers_count) {
        return round(($total_followers_count / $total_friends_count), 2);
    }
}
