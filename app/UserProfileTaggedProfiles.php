<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfileTaggedProfiles extends Model {
    protected $guarded = [];

    public function user_profile() {
        return $this->belongsTo('App\UserProfiles');
    }

    public function twitter_profile() {
        return $this->belongsTo('App\TwitterProfile');
    }

    public function tag() {
        return $this->belongsTo('App\Tags');
    }
}
