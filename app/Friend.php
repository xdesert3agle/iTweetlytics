<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model {
    protected $fillable = ['id', 'twitter_profile_id', 'id_str', 'name', 'screen_name', 'profile_image_url', 'location', 'follows_you', 'created_at', 'updated_at'];
}
