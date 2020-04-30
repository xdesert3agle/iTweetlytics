<?php

namespace App\Jobs;

use App\TwitterProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTwitterProfileRelationshipsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;

    /**
     * UpdateTwitterProfileRelationshipsJob constructor.
     * @param TwitterProfile $profile
     */
    public function __construct(TwitterProfile $profile) {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $all_followers = $this->profile->followers()->get();
        $all_friends = $this->profile->friends()->get();

        // Se intersectan ambas colecciones en ambas direcciones
        $followers_ids_im_following_back = $all_followers->pluck('id_str')->intersect($all_friends->pluck('id_str'));;
        $friends_ids_following_me_back = $all_friends->pluck('id_str')->intersect($all_followers->pluck('id_str'));

        // Se itera sobre los seguidos. Si el seguido sigue al perfil de vuelta => se marca en la base de datos
        foreach ($all_friends as $friend) {
            $is_following_me = !$friends_ids_following_me_back->filter(function($item) use ($friend) {
                return $item == $friend->id_str;
            })->isEmpty();

            $friend->is_following = $is_following_me;
            $friend->save();
        }

        // Lo mismo con los followers
        foreach ($all_followers as $follower) {
            $im_following_back = !$followers_ids_im_following_back->filter(function($item) use ($follower) {
                return $item == $follower->id_str;
            })->isEmpty();

            $follower->following = $im_following_back;
            $follower->save();
        }
    }
}
