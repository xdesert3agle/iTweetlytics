<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model {
    protected $guarded = [];

    public function twitter_profile() {
        return $this->belongsTo(TwitterProfile::class);
    }

    public function user_profile() {
        return $this->belongsTo(UserProfile::class);
    }
}
