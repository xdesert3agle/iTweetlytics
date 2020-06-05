<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckFieldToRelationshipTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('followers', function (Blueprint $table) {
            $table->boolean('is_present')->after('twitter_profile_id')->nullable();
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->boolean('is_present')->after('is_following_back')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

    }
}
