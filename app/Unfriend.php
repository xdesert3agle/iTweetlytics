<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unfriend extends Model {
    protected $guarded = [];

    public function twitter_profile() {
        return $this->belongsTo(TwitterProfile::class);
    }
}
