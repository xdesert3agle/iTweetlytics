<?php

namespace App\Console\Commands;

use App\Jobs\UpdateFollowersAndUnfollowers;
use App\Jobs\UpdateFriendsJob;
use App\TwitterProfile;
use Illuminate\Console\Command;

class UpdateFriends extends Command {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FRIENDS_PER_REQUEST = 5000;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friends:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $allProfiles = TwitterProfile::all();

        foreach ($allProfiles as $i => $profile) {

            // Número de peticiones necesarias para poder fetchear la lista completa de seguidos del perfil
            $neededFriendsJobs = ceil($profile->friends_count / (self::MAX_CONSECUTIVE_REQUESTS * self::FRIENDS_PER_REQUEST));

            for ($j = 0; $j < $neededFriendsJobs; $j++) {
                $delay = $j * self::REQUEST_WINDOW;
                $isLastJob = $j == ($neededFriendsJobs - 1) ? true : false;
                UpdateFriendsJob::dispatch($profile, $isLastJob)->delay(now()->addMinutes($delay));
            }
        }
    }
}
