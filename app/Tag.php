<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    protected $guarded = [];

    public static function cleanWords($words) {
        return preg_replace("/[^A-Za-z0-9 ]/", '', $words);
    }

    public function tagged() {
        return $this->belongsTo('App\UserProfileTaggedProfiles');
    }
}
