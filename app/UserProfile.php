<?php

namespace App;

use App\Helpers\ApiHelper;
use App\Helpers\UtilHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Thujohn\Twitter\Facades\Twitter;

class UserProfile extends Model {
    const REFRESH_COOLDOWN_SECS = 300;
    const REFRESH_COOLDOWN_MINS = 5;

    protected $fillable = [
        'user_id',
        'twitter_profile_id',
        'oauth_token',
        'oauth_token_secret',
        'next_followers_cursor',
        'next_friends_cursor',
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
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function twitter_profile() {
        return $this->belongsTo(TwitterProfile::class, 'twitter_profile_id');
    }

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

    public function tagged() {
        return $this->belongsTo('App\UserProfileTaggedProfiles');
    }

    // ------------------------------------------------ MUTATORS ------------------------------------------------ //
    public function getProfileImageUrlAttribute($value) {
        return str_replace('_normal', '', $value);
    }

    // ------------------------------------------------ UTILITY ------------------------------------------------ //
    public static function linkedByOtherUser($target) {
        if ($target instanceof \stdClass)
            $id = $target->id;
        else if (is_array($target))
            $id = $target['id'];
        else
            $id = $target;

        return self::where('twitter_profile_id', $id)->exists();
    }

    public static function linkToUser($twitter_profile, $tokens) {
        UserProfile::create([
            'user_id' => Auth::id(),
            'twitter_profile_id' => $twitter_profile->id,
            'oauth_token' => encrypt($tokens['oauth_token']),
            'oauth_token_secret' => encrypt($tokens['oauth_token_secret']),
        ]);
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

    public function hasReports() {
        return $this->reports->count() > 0;
    }

    public static function getUserUrlExpanded($user) {
        $urls = Url::where('user_profile_id', $user->id);

        foreach ($urls as $url) {
            if ($user->url == $url->short_url) {
                return $url->expanded_url;
            }
        }

        return null;
    }

    public function getTargetMatchingTags($target_user) {
        $user_tags = $this->tags;
        $found_tags = [];

        $targets = $target_user->getExpandedUrls(); // Urls

        if (isset($target_user->twitter_profile))
            $targets[] = $target_user->twitter_profile->description;
        else
            $targets[] = $target_user->description;

        foreach ($user_tags as $j => $tag) {
            foreach ($targets as $target) {

                // Comparación con las palabras
                if ($tag->words != null && preg_match_all($tag->words, strtolower($target))) {
                    $found_tags[] = $tag;
                    break;
                }

                // Comparación con las regexes
                if ($tag->regexes != null && preg_match_all($tag->regexes, $target)) {
                    $found_tags[] = $tag;
                    break;
                }
            }
        }

        return $found_tags;
    }

    public function refreshTags($tables = []) {
        foreach ($tables as $table) {
            $records = $table::where('user_profile_id', $this->id)->with('twitter_profile')->get();

            foreach ($records as $record) {
                $record->tags = self::getTargetMatchingTags($this, $record);
                $record->save();
            }
        }
    }

    public function getExpandedUrls() {
        return Url::where('twitter_profile_id', $this->id)->get()->pluck('expanded_url')->toArray();
    }

    public function generateDailyReport() {
        $all_followers = Follower::where('user_profile_id', $this->id)->get();
        $total_followers_count = $all_followers->count();
        $all_friends = Friend::where('user_profile_id', $this->id)->get();
        $total_friends_count = $all_friends->count();

        // Si tiene reports se genera el informe normalmente procesando todos los cambios
        if ($this->hasReports()) {
            $follows_count = Follow::where('user_profile_id', $this->id)->whereDate('created_at', Carbon::today())->count();
            $unfollows_count = Unfollow::where('user_profile_id', $this->id)->whereDate('created_at', Carbon::today())->count();
            $befriends_count = Befriend::where('user_profile_id', $this->id)->whereDate('created_at', Carbon::today())->count();
            $unfriends_count = Unfriend::where('user_profile_id', $this->id)->whereDate('created_at', Carbon::today())->count();

        } else { // Si es el primer informe no se cuentan las estadísticas actuales, pues acaba de sincronizar y no hay cambios
            $follows_count = 0;
            $unfollows_count = 0;
            $befriends_count = 0;
            $unfriends_count = 0;
        }

        Report::create([
            'user_profile_id' => $this->id,
            'follows' => $follows_count,
            'unfollows' => $unfollows_count,
            'followers_variation' => $follows_count - $unfollows_count,
            'befriends' => $befriends_count,
            'unfriends' => $unfriends_count,
            'total_followers' => $total_followers_count,
            'total_friends' => $total_friends_count,
            'followers_followback_percent' => Report::calcFollowbackPercentage($this, $total_friends_count),
            'user_followback_percent' => Report::calcUserFollowbackPercentage($this, $total_followers_count),
            'friends_to_followers_ratio' => Report::calcFriendsToFollowersRatio($total_friends_count, $total_followers_count),
            'report_date' => Carbon::today()->subDay()->toDateString()
        ]);
    }
}
