<?php

namespace App\Jobs;

use App\Follower;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersList implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get();
        $cursor = $this->profile->next_followers_cursor;
        $count = 0;

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        Twitter::reconfig([
            "token" => $this->profile->oauth_token,
            "secret" => $this->profile->oauth_token_secret,
        ]);

        do {
            $response = Twitter::getFollowersIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000]);
            $cursor = $response->next_cursor;

            foreach ($response->ids as $id) {
                $isFollower = $dbFollowers->firstWhere('twitter_user_id', $id) != null;

                // Se inserta si la ID no está entre los followers
                if (!$isFollower) {
                    $follower = new Follower;
                    $follower->twitter_profile_id = $this->profile->id;
                    $follower->twitter_user_id = $id;
                    $follower->save();
                }
            }

        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_followers_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();
    }

    protected function addToLog($string) {
        $file = Storage::get('file.txt');
        Storage::put('file.txt', $file . "$string\n");
    }
}
