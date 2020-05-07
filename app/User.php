<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Thujohn\Twitter\Twitter;

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
        'surname',
        'email',
        'password'
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

    public function twitter_profiles() {
        return $this->hasMany('App\TwitterProfile');
    }

    public function current_twitter_profile() {
        return $this->hasMany('App\TwitterProfile');
    }

    public function hasSyncProfiles() {
        return $this->twitter_profiles->count() > 0;
    }
}
