<?php

namespace App\Jobs;

use App\Follower;
use App\Follow;
use App\Report;
use App\Unfollow;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFollowersAndUnfollowers implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FOLLOWER_IDS_MAX_CONSECUTIVE_REQUESTS = 15;

    const FOLLOWERS_USERS_LOOKUP_MAX_CONSECUTIVE_REQUESTS = 900;
    const FOLLOWERS_USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;
    protected $isLast;

    public function __construct($profile, $isLast) {
        $this->profile = $profile;
        $this->isLast = $isLast;
    }

    public function handle() {
        $dbFollowers = Follower::where('twitter_profile_id', $this->profile->id)->get()->pluck('id_str')->toArray();
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
            $followers = Twitter::getFollowersIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $followers->next_cursor;

            $fetchedFollowers = array_merge($fetchedFollowers, $followers->ids);
        } while ($cursor != 0 && ++$count < self::FOLLOWER_IDS_MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

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
        $follows = Follow::where([
            ['twitter_profile_id', $this->profile->id],
        ])->whereDate('created_at', Carbon::today())->get();

        $unfollows = Unfollow::where([
            ['twitter_profile_id', $this->profile->id],
        ])->whereDate('created_at', Carbon::today())->get();

        $report = new Report;
        $report->twitter_profile_id = $this->profile->id;
        $report->follows = count($follows);
        $report->unfollows = count($unfollows);
        $report->followers_variation = $report->follows - $report->unfollows;
        $report->profile_total_followers = Follower::where('twitter_profile_id', $this->profile->id)->get()->count();

        $report->save();
    }

    protected function manageFollows($dbFollowers, $fetchedFollowers) {
        $newFollowers = array_diff($fetchedFollowers, $dbFollowers);
        $fetchedUsersLookup = $this->getFetchedUsersLookup($newFollowers);

        foreach ($fetchedUsersLookup as $user) {

            // Se registra el cambio
            $follow = new Follow;
            $follow->twitter_profile_id = $this->profile->id;
            $follow->id_str = $user->id_str;
            $follow->name = $user->name;
            $follow->screen_name = $user->screen_name;
            $follow->profile_image_url = $user->profile_image_url;
            $follow->save();

            // Nuevo follower a la lista
            $follower = new Follower;
            $follower->twitter_profile_id = $this->profile->id;
            $follower->id_str = $user->id_str;
            $follower->name = $user->name;
            $follower->screen_name = $user->screen_name;
            $follower->profile_image_url = $user->profile_image_url;
            $follower->followers_count = $user->followers_count;
            $follower->location = $user->location;
            $follower->save();
        }
    }

    protected function manageUnfollows($dbFollowers, $fetchedFollowers) {
        $newUnfollowers = array_diff($dbFollowers, $fetchedFollowers);
        $fetchedUsersLookup = $this->getFetchedUsersLookup($newUnfollowers);

        foreach ($fetchedUsersLookup as $user) {

            // Se registra el unfollow
            $unfollow = new Unfollow;
            $unfollow->twitter_profile_id = $this->profile->id;
            $unfollow->id_str = $user->id_str;
            $unfollow->name = $user->name;
            $unfollow->screen_name = $user->screen_name;
            $unfollow->profile_image_url = $user->profile_image_url;
            $unfollow->save();

            // Se elimina el usuario de la lista de followers
            Follower::where([
                ['twitter_profile_id', $this->profile->id],
                ['id_str', $user->id_str]
            ])->delete();
        }
    }

    protected function getFetchedUsersLookup($newFollowers) {
        $fetchedUsersLookup = [];
        $neededNamesReqCount = count($newFollowers) / self::FOLLOWERS_USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($neededNamesReqCount > 0)
            for ($i = 0; $i < ceil($neededNamesReqCount); $i++)
                $fetchedUsersLookup = array_merge($fetchedUsersLookup, Twitter::getUsersLookup(['user_id' => array_slice($newFollowers, $i * self::FOLLOWERS_USERS_LOOKUP_AMOUNT_PER_REQUEST, self::FOLLOWERS_USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetchedUsersLookup;
    }

    protected function addToLog($string) {
        $file = Storage::get('file.txt');
        Storage::put('file.txt', $file . "$string\n");
    }
}
