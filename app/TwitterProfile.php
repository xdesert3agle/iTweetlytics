<?php

namespace App;

use App\Helpers\ApiHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Thujohn\Twitter\Facades\Twitter;

class TwitterProfile extends Model {
    const REFRESH_COOLDOWN_SECS = 300;
    const REFRESH_COOLDOWN_MINS = 5;

    protected $fillable = [
        'id',
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
    public function synced_profile() {
        return $this->belongsToMany(UserProfile::class, 'followers', 'user_profile_id', 'twitter_profile_id');
    }

    public function friends() {
        return $this->belongsToMany(UserProfile::class, 'followers', 'user_profile_id', 'friends');
    }

    public function follows() {
        return $this->belongsToMany(UserProfile::class, 'followers', 'user_profile_id', 'follows');
    }

    public function unfollows() {
        return $this->belongsToMany(UserProfile::class, 'followers', 'user_profile_id', 'unfollows');
    }

    public function befriends() {
        return $this->belongsToMany(UserProfile::class, 'followers', 'user_profile_id', 'befriends');
    }

    public function unfriends() {
        return $this->belongsToMany(UserProfile::class, 'followers', 'user_profile_id', 'unfriends');
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
        $urls = Url::where('profile_id', $user->id);

        foreach ($urls as $url) {
            if ($user->url == $url->short_url) {
                return $url->expanded_url;
            }
        }

        return null;
    }


    public function getExpandedUrls() {
        return Url::where('twitter_profile_id', $this->id)->get()->pluck('expanded_url')->toArray();
    }

    public static function insertIfNewReduced($profile, $arr) {
        if ($arr instanceof \stdClass) // Si el parámetro no es un array se convierte a array
            $arr = get_object_vars($arr);

        TwitterProfile::firstOrCreate([
            'id' => isset($arr['id_str']) ? $arr['id_str'] : $arr['id']
        ]);
    }

    public static function insertIfNew($profile, $arr) {
        if ($arr instanceof \stdClass) // Si el parámetro no es un array se convierte a array
            $arr = get_object_vars($arr);

        return TwitterProfile::firstOrCreate(
            ['id' => isset($arr['id_str']) ? $arr['id_str'] : $arr['id']],
            [
                'id' => isset($arr['id_str']) ? $arr['id_str'] : $arr['id'],
                'name' => $arr['name'],
                'screen_name' => $arr['screen_name'],
                'description' => $arr['description'],
                'url' => $arr['url'],
                'location' => $arr['location'],
                'friends_count' => $arr['friends_count'],
                'followers_count' => $arr['followers_count'],
                'statuses_count' => $arr['statuses_count'],
                'listed_count' => $arr['listed_count'],
                'profile_image_url' => $arr['profile_image_url'],
                'profile_banner_url' => array_key_exists('profile_banner_url', $arr) ? $arr['profile_banner_url'] : "",
                'protected' => $arr['protected'],
                'verified' => $arr['verified'],
                'suspended' => 0,
                'lang' => $arr['lang']
            ]
        );
    }
}
