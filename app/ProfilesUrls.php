<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilesUrls extends Model {
    protected $guarded = [];

    public static function insertProfileUrls($user) {
        if (isset($user->entities->url)) {
            foreach ($user->entities as $urlPlace) {
                foreach ($urlPlace->urls as $url) {
                    ProfilesUrls::firstOrCreate([
                        'profile_id' => $user->id,
                        'short_url' => $url->url,
                        'expanded_url' => $url->expanded_url
                    ]);
                }
            }
        }
    }
}
