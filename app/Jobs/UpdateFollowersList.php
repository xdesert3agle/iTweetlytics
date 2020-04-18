<?php

namespace App\Jobs;

use App\Follower;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersList implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    protected $profile;

    /**
     * Create a new job instance.
     *
     * @param $profile
     */
    public function __construct($profile) {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
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

            // Se inserta una entrada en la tabla de followers por cada ID recogida en la petición
            foreach ($response->ids as $id) {
                $follower = new Follower;
                $follower->twitter_profiles_id = $this->profile->id;
                $follower->twitter_user_id = $id;
                $follower->save();
            }

        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();
    }
}
