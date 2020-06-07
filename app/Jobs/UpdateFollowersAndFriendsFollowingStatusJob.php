<?php

namespace App\Jobs;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Unfollow;
use App\Unfriend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateFollowersAndFriendsFollowingStatusJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CHUNK_SIZE = 10000;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $this->resetTablesRelationships();
        $this->updateFollowers();
        $this->updateFriends();
        $this->dispatchProfilesLookupJob();
    }

    protected function resetTablesRelationships() {
        Follower::where('user_profile_id', $this->profile->id)
            ->update(['is_followed_back' => false]);

        Follow::where('user_profile_id', $this->profile->id)
            ->update(['is_followed_back' => false]);

        Unfollow::where('user_profile_id', $this->profile->id)
            ->update(['is_followed_back' => false]);

        Friend::where('user_profile_id', $this->profile->id)
            ->update(['is_following_back' => false]);

        Befriend::where('user_profile_id', $this->profile->id)
            ->update(['is_following_back' => false]);

        Unfriend::where('user_profile_id', $this->profile->id)
            ->update(['is_following_back' => false]);
    }

    protected function updateFollowers() {
        $followers_count = Follower::where('user_profile_id', $this->profile->id)->count();
        $followers_chunk_size = $followers_count < self::MAX_CHUNK_SIZE ? $followers_count : self::MAX_CHUNK_SIZE;
        $num_followers_queries = ceil($followers_count / $followers_chunk_size);

        for ($i = 0; $i < $num_followers_queries; $i++) {
            $followers_chunk_profile_ids = Follower::where([
                'user_profile_id' => $this->profile->id,
            ])->skip($i * $followers_chunk_size)->take($followers_chunk_size)->pluck('twitter_profile_id')->all();

            Friend::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $followers_chunk_profile_ids)
                ->update(['is_following_back' => true]);

            Befriend::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $followers_chunk_profile_ids)
                ->update(['is_following_back' => true]);

            Unfriend::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $followers_chunk_profile_ids)
                ->update(['is_following_back' => true]);

            Follow::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $followers_chunk_profile_ids)
                ->update(['is_following_back' => true]);

            Unfollow::where('user_profile_id', $this->profile->id)
                ->whereIn('twitter_profile_id', $followers_chunk_profile_ids)
                ->update(['is_following_back' => true]);
        }
    }

    protected function updateFriends() {
        $friends_count = Friend::where('user_profile_id', $this->profile->id)->count();

        if ($friends_count > 0) {
            $friends_chunk_size = $friends_count < self::MAX_CHUNK_SIZE ? $friends_count : self::MAX_CHUNK_SIZE;
            $num_friends_queries = ceil($friends_count / $friends_chunk_size);

            for ($i = 0; $i < $num_friends_queries; $i++) {
                $friends_chunk_profile_ids = Friend::where([
                    'user_profile_id' => $this->profile->id,
                ])->skip($i * $friends_chunk_size)->take($friends_chunk_size)->pluck('twitter_profile_id')->all();

                Follower::where('user_profile_id', $this->profile->id)
                    ->whereIn('twitter_profile_id', $friends_chunk_profile_ids)
                    ->update(['is_followed_back' => true]);

                Follow::where('user_profile_id', $this->profile->id)
                    ->whereIn('twitter_profile_id', $friends_chunk_profile_ids)
                    ->update(['is_followed_back' => true]);

                Unfollow::where('user_profile_id', $this->profile->id)
                    ->whereIn('twitter_profile_id', $friends_chunk_profile_ids)
                    ->update(['is_followed_back' => true]);

                Befriend::where('user_profile_id', $this->profile->id)
                    ->whereIn('twitter_profile_id', $friends_chunk_profile_ids)
                    ->update(['is_followed_back' => true]);

                Unfriend::where('user_profile_id', $this->profile->id)
                    ->whereIn('twitter_profile_id', $friends_chunk_profile_ids)
                    ->update(['is_followed_back' => true]);
            }
        }
    }

    protected function dispatchProfilesLookupJob() {
        FetchProfilesLookup::dispatch($this->profile);
    }
}
