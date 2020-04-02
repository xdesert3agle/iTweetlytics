<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
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
