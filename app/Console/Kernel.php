<?php

namespace App\Console;

use App\Jobs\UpdateFollowersAndUnfollowers;
use App\TwitterProfile;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

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
        $schedule->command('followers:update')->daily();
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
