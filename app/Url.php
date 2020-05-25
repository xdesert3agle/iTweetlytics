<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model {
    protected $guarded = [];

    public static function insertProfileUrls($user) {
        if (isset($user->entities->url)) {
            foreach ($user->entities as $urlPlace) {
                foreach ($urlPlace->urls as $url) {
                    Url::firstOrCreate([
                        'twitter_profile_id' => $user->id_str,
                        'short_url' => $url->url,
                        'expanded_url' => $url->expanded_url
                    ]);
                }
            }
        }
    }
}
