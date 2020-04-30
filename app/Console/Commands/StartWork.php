<?php

namespace App\Console\Commands;

use App\Jobs\UpdateFollowersJob;
use App\TwitterProfile;
use Illuminate\Console\Command;

class StartWork extends Command {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work:start';

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
     * @return void
     */
    public function handle() {
        $all_profiles = TwitterProfile::all();

        foreach ($all_profiles as $i => $profile) {
            $profile->refresh();

            $needed_followers_jobs = ceil($profile->followers_count / (self::MAX_CONSECUTIVE_REQUESTS * self::FOLLOWERS_PER_REQUEST));

            for ($j = 0; $j < $needed_followers_jobs; $j++) {
                $followers_delay = $j * self::REQUEST_WINDOW;
                $is_last_job = $j == ($needed_followers_jobs - 1) ? true : false;

                UpdateFollowersJob::dispatch($profile, $is_last_job)->delay(now()->addMinutes($followers_delay));
            }
        }
    }
}
