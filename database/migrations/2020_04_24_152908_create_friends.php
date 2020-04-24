<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriends extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_profile_id', 20)->nullable();
            $table->string('id_str', 20)->nullable();
            $table->string('name')->nullable();
            $table->string('screen_name', 20)->nullable();
            $table->integer('followers_count')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();

            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });

        Schema::table('twitter_profiles', function (Blueprint $table) {
            $table->removeColumn('previous_followers_cursor');
            $table->string('next_friends_cursor', 20)->nullable()->after('next_followers_cursor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('friends');
    }
}
