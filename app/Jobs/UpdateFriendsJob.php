<?php

namespace App\Jobs;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Helpers\ApiHelper;
use App\TwitterProfile;
use App\Url;
use App\Report;
use App\SyncedProfile;
use App\Unfollow;
use App\Unfriend;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class UpdateFriendsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS = 15;
    const USERS_LOOKUP_AMOUNT_PER_REQUEST = 100;

    protected $profile;
    protected $is_last_job;

    public function __construct($profile, $is_last_job) {
        $this->profile = $profile;
        $this->is_last_job = $is_last_job;
    }

    public function handle() {
        $db_friends = Friend::where('synced_profile_id', $this->profile->id)->get()->pluck('twitter_profile_id')->toArray();
        $cursor = $this->profile->next_friends_cursor;
        $count = 0;
        $fetched_friends = [];

        // Se reconfigura la API de Twitter con los tokens de acceso del perfil
        ApiHelper::reconfig($this->profile);

        // Se fetchean los seguidos
        do {
            $friends = Twitter::getFriendsIds(['screen_name' => $this->profile->screen_name, 'cursor' => $cursor, 'count' => 5000, 'stringify_ids' => 'true']);
            $cursor = $friends->next_cursor;

            $fetched_friends = array_merge($fetched_friends, $friends->ids);
        } while ($cursor != 0 && ++$count < self::FRIENDS_IDS_MAX_CONSECUTIVE_REQUESTS); // Hasta que el cursor sea 0 o hasta límite de repeticiones

        // Se guarda el cursor si aún no se ha terminado de recorrer todas las páginas. Si no, se pone a -1
        $this->profile->next_friends_cursor = $cursor != 0 ? $cursor : -1;
        $this->profile->save();

        $this->registerNewFriends($db_friends, $fetched_friends);
        $this->registerUnfriends($db_friends, $fetched_friends);

        // Si es el último job => Se genera el reporte diario
        if ($this->is_last_job) {
            $this->profile->refreshTags([Follower::class, Follow::class, Unfollow::class, Friend::class, Befriend::class, Unfriend::class]);
            Report::generateDailyReport($this->profile);
        }
    }

    protected function registerNewFriends($db_friends, $fetched_friends_ids) {
        $new_friends = array_values(array_diff($fetched_friends_ids, $db_friends));

        if (!empty($new_friends)) {
            $rate_limits = Twitter::getAppRateLimit(['format' => 'array']);
            $available_requests = $rate_limits['resources']['users']['/users/lookup']['remaining'];
            $needed_requests = count($new_friends) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;
            $rate_reset_timestamp = $rate_limits['resources']['users']['/users/lookup']['reset'];

            if ($available_requests >= $needed_requests) {
                $fetched_friends_lookup = array_reverse($this->getLookupFromIdArray($new_friends));

                foreach ($fetched_friends_lookup as $i => $fetched_friend) {
                    $friend_fields = [
                        'id' => $fetched_friends_lookup,
                        'added_by' => $this->profile->id,
                        'name' => $fetched_friend->name,
                        'screen_name' => $fetched_friend->screen_name,
                        'description' => $fetched_friend->description,
                        'url' => $fetched_friend->url,
                        'location' => $fetched_friend->location,
                        'friends_count' => $fetched_friend->friends_count,
                        'followers_count' => $fetched_friend->followers_count,
                        'statuses_count' => $fetched_friend->statuses_count,
                        'listed_count' => $fetched_friend->listed_count,
                        'profile_image_url' => $fetched_friend->profile_image_url,
                        'profile_banner_url' => isset($fetched_friend->profile_banner_url) ? $fetched_friend->profile_banner_url : "",
                        'protected' => $fetched_friend->protected,
                        'verified' => $fetched_friend->verified,
                        'suspended' => 0,
                        'lang' => $fetched_friend->lang,
                    ];
                    TwitterProfile::insertIfNew($this->profile, $fetched_friend);
                    Url::insertProfileUrls($fetched_friend);

                    $relationship_basic_fields = [
                        'synced_profile_id' => $this->profile->id,
                        'twitter_profile_id' => $fetched_friend->id_str,
                        'tags' => SyncedProfile::getTagsFromProfile($this->profile, $fetched_friend)
                    ];

                    Befriend::create($relationship_basic_fields);

                    $relationship_basic_fields['hidden'] = 0;
                    Friend::create($relationship_basic_fields);
                }

            } elseif ($this->is_last_job) {
                $lookup_reset_time = Carbon::createFromTimestamp($rate_reset_timestamp);
                $delay = $lookup_reset_time->diffInSeconds(Carbon::now());

                FetchRemainingFriendsLookups::dispatch($this->profile)->delay($delay);

                foreach ($new_friends as $i => $new_friend_id) {
                    $basic_fields = [
                        'id' => $new_friend_id,
                        'added_by' => $this->profile->id,
                    ];

                    TwitterProfile::insertIfNewReduced($this->profile, $basic_fields);

                    $relationship_basic_fields = [
                        'synced_profile_id' => $this->profile->id,
                        'twitter_profile_id' => $new_friend_id,
                        'tags' => ""
                    ];

                    Befriend::create($relationship_basic_fields);

                    $relationship_basic_fields['hidden'] = 0;
                    Friend::create($relationship_basic_fields);
                }
            }
        }
    }

    protected function registerUnfriends($db_friends, $fetched_friends_ids) {
        $no_longer_friends_ids = array_diff($db_friends, $fetched_friends_ids);
        $no_longer_friends = Friend::whereIn('twitter_profile_id', $no_longer_friends_ids)->get();

        foreach ($no_longer_friends as $exfriend) {
            Unfriend::create([
                'synced_profile_id' => $this->profile->id,
                'twitter_profile_id' => $exfriend->id,
                'tags' => $exfriend->tags
            ]);

            $friend->delete();
        }
    }

    protected function getLookupFromIdArray($array) {
        $fetched_lookups = [];
        $needed_lookup_requests = count($array) / self::USERS_LOOKUP_AMOUNT_PER_REQUEST;

        if ($needed_lookup_requests > 0)
            for ($i = 0; $i < ceil($needed_lookup_requests); $i++)
                $fetched_lookups = array_merge($fetched_lookups, Twitter::getUsersLookup(['user_id' => array_slice($array, $i * self::USERS_LOOKUP_AMOUNT_PER_REQUEST, self::USERS_LOOKUP_AMOUNT_PER_REQUEST)]));

        return $fetched_lookups;
    }
}
