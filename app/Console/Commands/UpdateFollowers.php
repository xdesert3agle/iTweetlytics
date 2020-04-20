<?php

namespace App\Console\Commands;

use App\Jobs\UpdateFollowersAndUnfollowers;
use App\TwitterProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateFollowers extends Command {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followers:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registra y gestiona los cambios en los followers de los perfiles de Twitter.';

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
        $isLast = false;

        foreach ($allProfiles as $i => $profile) {

            // Se calcula el número de peticiones necesarias para poder fetchear la lista completa de followers del perfil
            $neededJobs = ceil($profile->followers_count / (self::MAX_CONSECUTIVE_REQUESTS * self::FOLLOWERS_PER_REQUEST));

            // Se mandan los jobs necesarios, con suficiente espacio entre ellos para no llegar al Rate Limit
            for ($j = 0; $j < $neededJobs; $j++) {
                $isLast = $j == ($neededJobs - 1) ? true : false;
                UpdateFollowersAndUnfollowers::dispatch($profile, $isLast)->delay(now()->addMinutes($j * self::REQUEST_WINDOW)->addSeconds(10));
            }
        }
    }
}
