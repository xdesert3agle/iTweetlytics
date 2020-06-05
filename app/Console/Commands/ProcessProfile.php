<?php

namespace App\Console\Commands;

use App\Jobs\FetchRemainingFriendsLookups;
use App\Jobs\UpdateFollowersJob;
use App\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ProcessProfile extends Command {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:process {target}';

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
        switch ($this->argument('target')) {
            case "all":
                $target = UserProfile::with('twitter_profile')->get();
                break;

            default:
                if (Str::startsWith($this->argument('target'), '@'))
                    $target = UserProfile::where('screen_name', $this->argument('target'))->with('twitter_profile')->first();
                else
                    $target = UserProfile::where('twitter_profile_id', $this->argument('target'))->with('twitter_profile')->get();

                break;
        }

        foreach ($target as $i => $profile) {
            $profile->refresh();
            UpdateFollowersJob::dispatch($profile);
        }
    }
}
