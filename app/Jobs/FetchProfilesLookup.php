<?php

namespace App\Jobs;

use App\Helpers\ApiHelper;
use App\TwitterProfile;
use App\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class FetchProfilesLookup implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;

    public function __construct($profile) {
        $this->profile = $profile;
    }

    public function handle() {
        $incomplete_profiles_count = TwitterProfile::whereNull('name')->count();
        $needed_requests_count = $incomplete_profiles_count / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;
        $remaining_requests_count = ApiHelper::getRateLimit('users', 'remaining');
        $loop_iter = min([$needed_requests_count, $remaining_requests_count]);

        for ($i = 0; $i < $loop_iter; $i++) {
            $profiles_chunk_ids = TwitterProfile::whereNull('name')->take(self::USERS_LOOKUP_AMOUNT_PER_REQUEST)->pluck('id')->all();
            $fetched_profiles_lookup = Twitter::getUsersLookup(['user_id' => $profiles_chunk_ids]);

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

        $incomplete_profiles_count = TwitterProfile::whereNull('name')->count();

        if ($incomplete_profiles_count == 0) { // Se pasa a la siguiente etapa
            ProcessTagsJob::dispatch($this->profile);

        } else { // Se envía otro job para que siga recolectando
            $next_job_delay = ApiHelper::getRateLimit('users', 'reset');
            FetchProfilesLookup::dispatch($this->profile)->delay($next_job_delay);
        }
    }
}
