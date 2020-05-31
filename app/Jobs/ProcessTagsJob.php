<?php

namespace App\Jobs;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Report;
use App\Unfollow;
use App\Unfriend;
use App\UserProfileTaggedProfiles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTagsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $followers = Follower::where('user_profile_id', 1)->with('twitter_profile');
        $follows = Follow::where('user_profile_id', 1)->with('twitter_profile');
        $unfollows = Unfollow::where('user_profile_id', 1)->with('twitter_profile');
        $friends = Friend::select('id', 'user_profile_id', 'twitter_profile_id', 'created_at', 'updated_at')->where('user_profile_id', 1)->with('twitter_profile');
        $unfriends = Unfriend::where('user_profile_id', 1)->with('twitter_profile');

        $all_profiles = Befriend::where('user_profile_id', 1)
            ->with('twitter_profile')
            ->union($followers)
            ->union($follows)
            ->union($unfollows)
            ->union($friends)
            ->union($unfriends)
            ->get()
            ->pluck('twitter_profile');

        foreach ($all_profiles as $twitter_profile) {
            $matchingTags = $this->profile->getTargetMatchingTags($twitter_profile);

            foreach ($matchingTags as $tag) {
                UserProfileTaggedProfiles::firstOrCreate([
                    'user_profile_id' => $this->profile->id,
                    'twitter_profile_id' => $twitter_profile->id,
                    'tag_id' => $tag->id,
                ]);
            }
        }

        GenerateDailyReportJob::dispatch($this->profile);
    }
}
