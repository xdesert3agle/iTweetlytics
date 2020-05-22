<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model {
    protected $fillable = ['id', 'twitter_profile_id', 'id_str', 'name', 'screen_name', 'description', 'followers_count', 'profile_image_url', 'location', 'tags', 'created_at', 'updated_at'];
}
