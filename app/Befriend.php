<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Befriend extends Model {
    protected $guarded = [];

    public function twitter_profile() {
        return $this->belongsTo(TwitterProfile::class);
    }
}
