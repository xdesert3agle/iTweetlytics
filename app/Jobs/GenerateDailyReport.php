<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDailyReport implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $yesterdaysReport = DailyReport::where('twitter_profile_id');

        $report = new DailyReport;
        $report->followers_count = $this->profile->followers_count;
        $report->followers_variation = $this->profile->followers_count - 1;
    }
}
