<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model {
    protected $fillable = ['id', 'twitter_profile_id', 'id_str', 'name', 'screen_name', 'description', 'profile_image_url', 'location', 'tags', 'created_at', 'updated_at'];
}
