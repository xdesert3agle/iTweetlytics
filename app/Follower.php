<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model {
    protected $guarded = [];

    public function twitter_profile() {
        return $this->belongsTo(TwitterProfile::class);
    }
}
