<?php

namespace App\Jobs;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Report;
use App\TwitterProfile;
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

    const AMOUNT_PER_ITER = 500;
    const AMOUNT_PER_TABLE = 500;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $amounts[] = TwitterProfile::whereIn('id', Follower::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->count();
        $amounts[] = TwitterProfile::whereIn('id', Follow::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->count();
        $amounts[] = TwitterProfile::whereIn('id', Unfollow::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->count();
        $amounts[] = TwitterProfile::whereIn('id', Friend::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->count();
        $amounts[] = TwitterProfile::whereIn('id', Unfriend::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->count();
        $amounts[] = TwitterProfile::whereIn('id', Unfriend::where('user_profile_id', 1)->pluck('twitter_profile_id'))->count();

        $max_amount = max($amounts);
        $needed_iter = ceil($max_amount / self::AMOUNT_PER_ITER);

        for ($i = 0; $i < $needed_iter; $i++) {
            $followers = TwitterProfile::whereIn('id', Follower::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->take(self::AMOUNT_PER_TABLE)->skip($i * self::AMOUNT_PER_TABLE);
            $follows = TwitterProfile::whereIn('id', Follow::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->take(self::AMOUNT_PER_TABLE)->skip($i * self::AMOUNT_PER_TABLE);
            $unfollows = TwitterProfile::whereIn('id', Unfollow::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->take(self::AMOUNT_PER_TABLE)->skip($i * self::AMOUNT_PER_TABLE);
            $friends = TwitterProfile::whereIn('id', Friend::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->take(self::AMOUNT_PER_TABLE)->skip($i * self::AMOUNT_PER_TABLE);
            $unfriends = TwitterProfile::whereIn('id', Unfriend::where('user_profile_id', $this->profile->id)->pluck('twitter_profile_id'))->take(self::AMOUNT_PER_TABLE)->skip($i * self::AMOUNT_PER_TABLE);
            $all_profiles = TwitterProfile::whereIn('id', Unfriend::where('user_profile_id', 1)->pluck('twitter_profile_id'))->take(self::AMOUNT_PER_TABLE)->skip($i * self::AMOUNT_PER_TABLE)
                ->union($followers)
                ->union($follows)
                ->union($unfollows)
                ->union($friends)
                ->union($unfriends)
                ->get();

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
        }

        GenerateDailyReportJob::dispatch($this->profile);
    }
}
