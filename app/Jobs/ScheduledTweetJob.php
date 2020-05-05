<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class ScheduledTweetJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TWEET_STATUS_SENT = 'sent';

    protected $tweet;

    /**
     * Create a new job instance.
     *
     * @param $tweet
     */
    public function __construct($tweet) {
        $this->tweet = $tweet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        Twitter::postTweet(['status' => $this->tweet->tweet_content]);

        $this->tweet->status = self::TWEET_STATUS_SENT;
        $this->tweet->save();
    }
}
