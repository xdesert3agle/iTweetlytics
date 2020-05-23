<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterProfilesTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_profiles_tags', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_profile_id', 20)->nullable();
            $table->string('tag')->nullable();
            $table->string('words')->nullable();

            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_profiles_tags');
    }
}
