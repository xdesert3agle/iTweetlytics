<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowingFieldOnFriends extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('friends', function (Blueprint $table) {
            $table->string('is_following_back')->nullable()->after('twitter_profile_id');
        });

        Schema::table('befriends', function (Blueprint $table) {
            $table->string('is_following_back')->nullable()->after('twitter_profile_id');
        });

        Schema::table('unfriends', function (Blueprint $table) {
            $table->string('is_following_back')->nullable()->after('twitter_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
