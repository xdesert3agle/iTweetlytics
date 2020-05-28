<?php

namespace App;

use App\Helpers\ApiHelper;
use App\Helpers\UtilHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Thujohn\Twitter\Facades\Twitter;

class SyncedProfile extends Model {
    const REFRESH_COOLDOWN_SECS = 300;
    const REFRESH_COOLDOWN_MINS = 5;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'screen_name',
        'description',
        'url',
        'location',
        'friends_count',
        'followers_count',
        'statuses_count',
        'listed_count',
        'profile_image_url',
        'profile_banner_url',
        'protected',
        'verified',
        'suspended',
        'lang',
        'next_followers_cursor',
        'next_friends_cursor',
        'oauth_token',
        'oauth_token_secret',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // ---------------------------------------------- RELATIONSHIPS --------------------------------------------- //
    public function followers() {
        return $this->belongsToMany(TwitterProfile::class, 'followers');
    }

    public function friends() {
        return $this->belongsToMany(TwitterProfile::class, 'friends');
    }

    public function follows() {
        return $this->belongsToMany(TwitterProfile::class, 'follows');
    }

    public function unfollows() {
        return $this->belongsToMany(TwitterProfile::class, 'unfollows');
    }

    public function befriends() {
        return $this->belongsToMany(TwitterProfile::class, 'befriends');
    }

    public function unfriends() {
        return $this->belongsToMany(TwitterProfile::class, 'unfriends');
    }

    public function reports() {
        return $this->hasMany('App\Report');
    }

    public function scheduled_tweets() {
        return $this->hasMany('App\ScheduledTweet');
    }

    public function tags() {
        return $this->hasMany('App\Tag');
    }

    // ------------------------------------------------ MUTATORS ------------------------------------------------ //
    public function getProfileImageUrlAttribute($value) {
        return str_replace('_normal', '', $value);
    }

    // ------------------------------------------------ UTILITY ------------------------------------------------ //
    public static function assignToUser($credentials, $tokens) {
        $profile_fields = (array)$credentials;
        $profile_fields['user_id'] = Auth::id();
        $profile_fields['oauth_token'] = encrypt($tokens['oauth_token']);
        $profile_fields['oauth_token_secret'] = encrypt($tokens['oauth_token_secret']);
        SyncedProfile::create($profile_fields);
    }

    public function belongsToUser($userId) {
        return $this->user_id == $userId;
    }

    public function secsSinceLastRefresh() {
        $to = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at);

        return $to->diffInSeconds($from);
    }

    public function secsUntilRefresh() {
        return self::REFRESH_COOLDOWN_SECS - $this->secsSinceLastRefresh();
    }

    public function minsSinceLastRefresh() {
        $to = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at);

        return $to->diffInMinutes($from);
    }

    public function minsUntilRefresh() {
        return self::REFRESH_COOLDOWN_MINS - $this->minsSinceLastRefresh();
    }

    public function canBeRefreshed() {
        return $this->secsSinceLastRefresh() >= self::REFRESH_COOLDOWN_SECS;
    }

    public function refresh() {
        ApiHelper::reconfig($this);
        $this->fill(collect(Twitter::getCredentials())->toArray());
        $this->save();
    }

    public static function getUserUrlExpanded($user) {
        $urls = Url::where('synced_profile_id', $user->id);

        foreach ($urls as $url) {
            if ($user->url == $url->short_url) {
                return $url->expanded_url;
            }
        }

        return null;
    }

    public static function getTagsFromProfile($profile, $target_user) {
        $user_tags = Tag::where('synced_profile_id', $profile->id)->get();
        $found_tags = [];

        $targets = $profile->getExpandedUrls(); // Urls
        $targets[] = $target_user->twitter_profile->description; // Descripción

        foreach ($user_tags as $j => $tag) {
            foreach ($targets as $target) {

                // Comparación con las palabras
                if ($tag->words != null && preg_match($tag->words, strtolower($target))) {
                    $found_tags[] = $tag->tag;
                    break;
                }

                // Comparación con las regexes
                if ($tag->regexes != null && preg_match($tag->regexes, $target)) {
                    $found_tags[] = $tag->tag;
                    break;
                }
            }
        }

        return implode(", ", $found_tags);
    }

    public function refreshTags($tables = []) {
        foreach ($tables as $table) {
            $records = $table::where('synced_profile_id', $this->id)->with('twitter_profile')->get();

            foreach ($records as $record) {
                $record->tags = self::getTagsFromProfile($this, $record);
                $record->save();
            }
        }
    }

    public function getExpandedUrls() {
        return Url::where('twitter_profile_id', $this->id)->get()->pluck('expanded_url')->toArray();
    }
}
