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
        'user_id',
        'name',
        'screen_name',
        'location',
        'description',
        'protected',
        'followers_count',
        'friends_count',
        'listed_count',
        'favourites_count',
        'time_zone',
        'geo_enabled',
        'verified',
        'statuses_count',
        'profile_background_color',
        'profile_image_url',
        'profile_banner_url',
        'profile_link_color',
        'lang',
        'suspended',
        'oauth_token',
        'oauth_token_secret'
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
        return $this->hasMany('App\Follower');
    }

    public function friends() {
        return $this->hasMany('App\Friend');
    }

    public function follows() {
        return $this->hasMany('App\Follow');
    }

    public function unfollows() {
        return $this->hasMany('App\Unfollow');
    }

    public function befriends() {
        return $this->hasMany('App\Befriend');
    }

    public function unfriends() {
        return $this->hasMany('App\Unfriend');
    }

    public function reports() {
        return $this->hasMany('App\Report');
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
}
