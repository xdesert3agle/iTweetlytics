<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnfriends extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('unfriends', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_profile_id', 20)->nullable();
            $table->string('id_str', 20)->nullable();
            $table->string('name')->nullable();
            $table->string('screen_name', 20)->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();

            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('unfriends');
    }
}
