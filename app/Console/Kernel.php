<?php

namespace App\Console;

use App\Jobs\UpdateFollowersList;
use App\TwitterProfile;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule) {
        $schedule->call(function () {
            $this->fetchFollowers();

        })->daily();
    }

    protected function fetchFollowers() {
        $allProfiles = TwitterProfile::all();

        foreach ($allProfiles as $profile) {

            // Se calcula el número de peticiones necesarias para poder fetchear la lista completa de followers del perfil
            $neededJobs = $profile->followers_count / (self::MAX_CONSECUTIVE_REQUESTS * self::FOLLOWERS_PER_REQUEST);

            // Se mandan los jobs necesarios, con suficiente espacio entre ellos para no llegar al Rate Limit
            for ($i = 0; $i < ceil($neededJobs); $i++) {
                UpdateFollowersList::dispatch($profile)->delay(now()->addMinutes($i * self::REQUEST_WINDOW));
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
