<?php

namespace App\Jobs;

use App\Follower;
use App\Unfollower;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersAndUnfollowers implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $fetchedFollowers = [];

        $cursor = $this->profile->next_followers_cursor;
        $count = 0;

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        Twitter::reconfig([
            "token" => $this->profile->oauth_token,
            "secret" => $this->profile->oauth_token_secret,
        ]);

        do {
            $response = Twitter::getFollowersIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $response->next_cursor;

            $fetchedFollowers = array_merge($fetchedFollowers, $response->ids);
        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_followers_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();

        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get()->pluck('twitter_user_id')->toArray();

        $newUnfollowers = $this->getNewUnfollowers($dbFollowers, $fetchedFollowers);
        foreach ($newUnfollowers as $unfollowerId) {
            $unfollower = new Unfollower;
            $unfollower->twitter_profile_id = $this->profile->id;
            $unfollower->twitter_user_id = $unfollowerId;
            $unfollower->save();

            Follower::where([
                ['twitter_profile_id', $this->profile->id],
                ['twitter_user_id', $unfollowerId]
            ])->delete();
        }

        $newFollowers = $this->getNewFollowers($dbFollowers, $fetchedFollowers);
        foreach ($newFollowers as $followerId) {
            $follower = new Follower;
            $follower->twitter_profile_id = $this->profile->id;
            $follower->twitter_user_id = $followerId;
            $follower->save();
        }
    }

    protected function getNewUnfollowers($dbFollowers, $fetchedFollowers) {
        print_r($dbFollowers);
        print_r($fetchedFollowers);
        return array_diff($dbFollowers, $fetchedFollowers);
    }

    protected function getNewFollowers($dbFollowers, $fetchedFollowers) {
        return array_diff($fetchedFollowers, $dbFollowers);
    }
}
