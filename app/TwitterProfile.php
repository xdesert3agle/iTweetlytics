<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterProfile extends Model {
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'screen_name',
        'location',
        'description',
        'protected',
        'followers_count',
        'friends_count',
        'listed_count',
        'favourites_count',
        'time_zone',
        'geo_enabled',
        'verified',
        'statuses_count',
        'profile_background_color',
        'profile_image_url',
        'profile_banner_url',
        'profile_link_color',
        'lang',
        'suspended',
        'oauth_token',
        'oauth_token_secret'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
