<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReports extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_profile_id', 20)->nullable();
            $table->integer('followers')->nullable();
            $table->integer('unfollowers')->nullable();
            $table->integer('followers_variation')->nullable();
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
        Schema::dropIfExists('reports');
    }
}
