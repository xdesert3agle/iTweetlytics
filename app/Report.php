<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    protected $guarded = [];

    public static function calcFollowbackPercentage($profile, $total_friends_count) {
        $users_following_count = Friend::where('user_profile_id', $profile->id)
            ->whereIn('twitter_profile_id', Follower::where('user_profile_id', $profile->id)->get()->pluck('twitter_profile_id'))
            ->count();

        return round(($users_following_count / $total_friends_count) * 100, 2);
    }

    public static function calcUserFollowbackPercentage($profile, $total_followers_count) {
        $followers_following_back = Follower::where('user_profile_id', $profile->id)
            ->whereIn('twitter_profile_id', Friend::where('user_profile_id', $profile->id)->get()->pluck('twitter_profile_id'))
            ->count();

        return round(($followers_following_back / $total_followers_count) * 100, 2);
    }

    public static function calcFriendsToFollowersRatio($total_friends_count, $total_followers_count) {
        return round(($total_followers_count / $total_friends_count), 2);
    }
}
