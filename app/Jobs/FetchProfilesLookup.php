<?php

namespace App\Jobs;

use App\TwitterProfile;
use App\Url;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class FetchProfilesLookup implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 900;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;
    const MAX_USERS_TAKE_AMOUNT = 1000000;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }
t
    public function handle() {
        $incomplete_profiles = TwitterProfile::whereNull('name')->take(self::MAX_USERS_TAKE_AMOUNT)->get()->pluck('id')->toArray();
        $needed_requests_count = ceil(count($incomplete_profiles) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST);
        $remaining_requests_count = Twitter::getAppRateLimit(['format' => 'array'])['resources']['users']['/users/lookup']['remaining'];
        $needs_multiple_jobs = $needed_requests_count > $remaining_requests_count;

        $loop_iters = min([$needed_requests_count, $remaining_requests_count]);

        for ($i = 0; $i < $loop_iters; $i++) {
            $offset = $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST;
            $amount = self::USERS_LOOKUP_AMOUNT_PER_REQUEST;
            $fetched_profiles_lookup = Twitter::getUsersLookup(['user_id' => array_slice($incomplete_profiles, $offset, $amount)]);

            foreach ($fetched_profiles_lookup as $fetched_profile) {
                TwitterProfile::where('id', $fetched_profile->id_str)
                    ->update([
                        'name' => $fetched_profile->name,
                        'screen_name' => $fetched_profile->screen_name,
                        'description' => $fetched_profile->description,
                        'url' => $fetched_profile->url,
                        'location' => $fetched_profile->location,
                        'friends_count' => $fetched_profile->friends_count,
                        'followers_count' => $fetched_profile->followers_count,
                        'statuses_count' => $fetched_profile->statuses_count,
                        'listed_count' => $fetched_profile->listed_count,
                        'profile_image_url' => $fetched_profile->profile_image_url,
                        'profile_banner_url' => isset($fetched_profile->profile_banner_url) ? $fetched_profile->profile_banner_url : null,
                        'protected' => $fetched_profile->protected,
                        'verified' => $fetched_profile->verified,
                        'suspended' => 0,
                        'lang' => $fetched_profile->lang
                    ]);

                Url::insertProfileUrls($fetched_profile);
            }
        }

        if (!$needs_multiple_jobs) { // Se pasa a la siguiente etapa
            ProcessTagsJob::dispatch($this->profile);

        } else { // Se envía otro job para que siga recolectando
            $users_lookup_reset_timestamp = Twitter::getAppRateLimit(['format' => 'array'])['resources']['users']['/users/lookup']['reset'];
            $delay = Carbon::createFromTimestamp($users_lookup_reset_timestamp)->diffInSeconds();

            FetchProfilesLookup::dispatch($this->profile)->delay($delay);
        }
    }
}
