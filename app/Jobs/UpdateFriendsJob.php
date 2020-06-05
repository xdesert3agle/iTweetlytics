<?php

namespace App\Jobs;

use App\Follower;
use App\TwitterProfile;
use App\Befriend;
use App\Friend;
use App\Unfriend;
use App\Helpers\ApiHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFriendsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;

    protected $profile;
    protected $fetched_friends;
    protected $job_info;

    public function __construct($profile, $fetched_friends, $job_info) {
        $this->profile = $profile;
        $this->fetched_friends = $fetched_friends;
        $this->job_info = $job_info;
    }

    public function handle() {
        $this->fetchFriends(); // El resultado se guarda en $this->fetched_friends

        // Si es el último job de la cadena => Se registran comienza el procesamiento
        if ($this->job_info['id'] == $this->job_info['last_job_id']) {
            $db_friends = friend::where('user_profile_id', $this->profile->id)
                ->get()
                ->pluck('twitter_profile_id')
                ->toArray();

            $this->registerBefriends($db_friends);
            $this->registerUnfriends($db_friends);
            FetchProfilesLookup::dispatch($this->profile);

        } else { // Si no es el último job se manda el siguiente
            $this->job_info['id'] += 1;
            $next_job_delay = $this->job_info['id'] * self::REQUEST_WINDOW;
            UpdateFriendsJob::dispatch($this->profile, $this->fetched_friends, $this->job_info)->delay(now()->addMinutes($next_job_delay));
        }
    }

    protected function fetchFriends() {
        $cursor = $this->profile->next_friends_cursor;
        $count = 0;

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        ApiHelper::reconfig($this->profile);

        // Se fetchean los seguidos
        do {
            $friends = Twitter::getFriendsIds(['screen_name' => $this->profile->twitter_profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $friends->next_cursor;

            $this->fetched_friends = array_merge($this->fetched_friends, $friends->ids);
        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_friends_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();
    }

    protected function registerBefriends($db_friends) {
        $new_friends_ids = array_reverse(array_diff($this->fetched_friends, $db_friends));

        foreach ($new_friends_ids as $new_friend_id) {
            TwitterProfile::insertReducedIfNew($new_friend_id);

            $fields = [
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $new_friend_id,
                'is_following_back' => Follower::where([
                    'user_profile_id' => $this->profile->id,
                    'twitter_profile_id' => $new_friend_id
                ])->count() ? true : false
            ];

            Befriend::create($fields);
            Friend::create($fields);
        }
    }

    protected function registerUnfriends($db_friends) {
        $new_unfriends = array_diff($db_friends, $this->fetched_friends);
        $new_unfriends_hydrated = Friend::whereIn('id', $new_unfriends)->get();

        foreach ($new_unfriends_hydrated as $unfriend) {
            $exfriend = Friend::where([
                ['user_profile_id', $this->profile->id],
                ['id', $unfriend->id]
            ])->get();

            Unfriend::create([
                'user_profile_id' => $this->profile->id,
                'twitter_profile_id' => $unfriend->id,
                'is_following_back' => $exfriend->is_following_you
            ]);

            $exfriend->delete();
        }
    }
}
