<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeTablesAddRelationshipsFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('followers', function (Blueprint $table) {
            $table->boolean('is_followed_back')->nullable()->after('twitter_profile_id');
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->boolean('is_following_back')->nullable()->after('twitter_profile_id');
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->boolean('is_followed_back')->nullable()->after('twitter_profile_id');
            $table->boolean('is_following_back')->nullable()->after('is_followed_back');
        });

        Schema::table('unfollows', function (Blueprint $table) {
            $table->boolean('is_followed_back')->nullable()->after('twitter_profile_id');
            $table->boolean('is_following_back')->nullable()->after('is_followed_back');
        });

        Schema::table('befriends', function (Blueprint $table) {
            $table->boolean('is_followed_back')->nullable()->after('twitter_profile_id');
            $table->boolean('is_following_back')->nullable()->after('is_followed_back');
        });

        Schema::table('unfriends', function (Blueprint $table) {
            $table->boolean('is_followed_back')->nullable()->after('twitter_profile_id');
            $table->boolean('is_following_back')->nullable()->after('is_followed_back');
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->boolean('is_present')->after('twitter_profile_id')->default(true)->nullable();
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->boolean('is_present')->after('is_following_back')->default(true)->nullable();
        });
    }

    /**
     * Reverse the migrations.s
     *
     * @return void
     */
    public function down() {
        //
    }
}
