<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model {
    protected $guarded = [];

    public function twitter_profile() {
        return $this->belongsTo(TwitterProfile::class);
    }

    public function user_profile() {
        return $this->belongsTo(UserProfile::class);
    }

    public function tagged() {
        return $this->belongsTo(UserProfileTaggedProfiles::class);
    }
}
