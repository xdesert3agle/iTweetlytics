<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model {
    protected $guarded = [];

    public static function insertProfileUrls($new_profile) {
        if (isset($new_profile->entities->url)) {
            foreach ($new_profile->entities as $urlPlace) {
                foreach ($urlPlace->urls as $url) {
                    Url::firstOrCreate([
                        'twitter_profile_id' => $new_profile->id_str,
                        'short_url' => $url->url,
                        'expanded_url' => $url->expanded_url
                    ]);
                }
            }
        }
    }
}
