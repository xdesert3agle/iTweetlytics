<?php

namespace App\Jobs;

use App\Follower;
use App\ProfileChange;
use App\Report;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersAndUnfollowers implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    const ACTION_FOLLOW = 'follow';
    const ACTION_UNFOLLOW = 'unfollow';

    protected $profile;
    protected $isLast;

    public function __construct($profile, $isLast) {
        $this->profile = $profile;
        $this->isLast = $isLast;
    }

    public function handle() {
        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get()->pluck('twitter_user_id')->toArray();
        $fetchedFollowers = [];

        $cursor = $this->profile->next_followers_cursor;
        $count = 0;

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        Twitter::reconfig([
            "token" => $this->profile->oauth_token,
            "secret" => $this->profile->oauth_token_secret,
        ]);

        // Se fetchean los seguidores
        do {
            $response = Twitter::getFollowersIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $response->next_cursor;

            $fetchedFollowers = array_merge($fetchedFollowers, $response->ids);
        } while ($cursor != 0 && ++$count < self::MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_followers_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();

        $this->manageFollows($dbFollowers, $fetchedFollowers);
        $this->manageUnfollows($dbFollowers, $fetchedFollowers);

        if ($this->isLast) {
            $this->generateDailyReport();
        }
    }

    protected function generateDailyReport() {
        $profileChanges = ProfileChange::where([
            ['twitter_profile_id', $this->profile->id],
        ])->whereDate('created_at', Carbon::today())->get();

        $report = new Report;
        $report->twitter_profile_id = $this->profile->id;
        $report->follows = count($profileChanges->where('action', self::ACTION_FOLLOW));
        $report->unfollows = count($profileChanges->where('action', self::ACTION_UNFOLLOW));
        $report->followers_variation = $report->follows - $report->unfollows;
        $report->profile_total_followers = Follower::where('twitter_profile_id', $this->profile->id)->get()->count();

        $report->save();
    }

    protected function manageFollows($dbFollowers, $fetchedFollowers) {
        $newFollowers = array_diff($fetchedFollowers, $dbFollowers);

        foreach ($newFollowers as $followerId) {

            // Nuevo follower a la lista
            $follower = new Follower;
            $follower->twitter_profile_id = $this->profile->id;
            $follower->twitter_user_id = $followerId;
            $follower->save();

            // Se registra el cambio
            $profileChange = new ProfileChange;
            $profileChange->twitter_profile_id = $this->profile->id;
            $profileChange->twitter_user_id = $followerId;
            $profileChange->action = self::ACTION_FOLLOW;
            $profileChange->save();
        }
    }

    protected function manageUnfollows($dbFollowers, $fetchedFollowers) {
        $newUnfollowers = array_diff($dbFollowers, $fetchedFollowers);

        foreach ($newUnfollowers as $unfollowerId) {
            // Se registra el cambio
            $profileChange = new ProfileChange;
            $profileChange->twitter_profile_id = $this->profile->id;
            $profileChange->twitter_user_id = $unfollowerId;
            $profileChange->action = self::ACTION_UNFOLLOW;
            $profileChange->save();

            // Se elimina el usuario de la lista de followers
            Follower::where([
                ['twitter_profile_id', $this->profile->id],
                ['twitter_user_id', $unfollowerId]
            ])->delete();
        }
    }

    protected function addToLog($string) {
        $file = Storage::get('file.txt');
        Storage::put('file.txt', $file . "$string\n");
    }
}
