<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SplitProfileChangesIntoFollowsAndUnfollows extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('profile_changes', function ($table) {
            $table->dropColumn('action');
        });

        Schema::rename('profile_changes', 'follows');

        Schema::create('unfollows', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_profile_id', 20)->nullable();
            $table->unsignedBigInteger('report_id')->nullable();
            $table->string('id_str', 20)->nullable();
            $table->string('name')->nullable();
            $table->string('screen_name', 20)->nullable();
            $table->string('profile_image_url')->nullable();
            $table->timestamps();

            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
            $table->foreign('report_id')->references('id')->on('reports');
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
