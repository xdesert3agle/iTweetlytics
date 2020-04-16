<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_profiles_id', 20)->nullable();
            $table->string('twitter_user_id', 20)->nullable();
            $table->string('screen_name', 20)->nullable();

            $table->foreign('twitter_profiles_id')->references('id')->on('twitter_profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('followers');
    }
}
