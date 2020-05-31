<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseRework extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('followers');
        Schema::dropIfExists('follows');
        Schema::dropIfExists('unfollows');
        Schema::dropIfExists('friends');
        Schema::dropIfExists('befriends');
        Schema::dropIfExists('unfriends');
        Schema::dropIfExists('twitter_profiles_tags');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('user_profiles_urls');
        Schema::dropIfExists('scheduled_tweets');
        Schema::dropIfExists('twitter_profiles');

        Schema::create('twitter_profiles', function (Blueprint $table) {
            $table->string('id', 25);
            $table->string('name')->nullable();
            $table->string('screen_name')->nullable();
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->string('location')->nullable();
            $table->integer('friends_count')->nullable();
            $table->integer('followers_count')->nullable();
            $table->integer('statuses_count')->nullable();
            $table->integer('listed_count')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('profile_banner_url')->nullable();
            $table->boolean('protected')->nullable();
            $table->boolean('verified')->nullable();
            $table->boolean('suspended')->nullable();
            $table->string('lang')->nullable();
            $table->timestamps();

            $table->primary('id');
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('twitter_profile_id', 25);
            $table->string('oauth_token', 512)->nullable();
            $table->string('oauth_token_secret', 512)->nullable();
            $table->string('next_followers_cursor')->default(-1)->nullable();
            $table->string('next_friends_cursor')->default(-1)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
            $table->unique(['user_id', 'twitter_profile_id']);
        });

        Schema::create('followers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
            $table->unique(['user_profile_id', 'twitter_profile_id']);
        });

        Schema::create('follows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });

        Schema::create('unfollows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->boolean('hidden');
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
            $table->unique(['user_profile_id', 'twitter_profile_id']);
        });

        Schema::create('befriends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });

        Schema::create('unfriends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_profile_id');
            $table->string('tag')->nullable();
            $table->string('words')->nullable();
            $table->text('regexes')->nullable();
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->unique(['user_profile_id', 'tag']);
        });

        Schema::create('user_profile_tagged_profiles', function (Blueprint $table) {
            $table->foreignId('user_profile_id');
            $table->string('twitter_profile_id', 25);
            $table->foreignId('tag_id');

            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
            $table->foreign('tag_id')->references('id')->on('tags');
            $table->unique(['user_profile_id', 'twitter_profile_id', 'tag_id'], 'user_profile_tagged_profiles_user_profile_tags_profiles_unique');
        });

        Schema::create('urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('twitter_profile_id', 25);
            $table->string('short_url');
            $table->string('expanded_url');
            $table->timestamps();

            $table->foreign('twitter_profile_id')->references('id')->on('twitter_profiles');
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->nullable();
            $table->foreignId('user_profile_id');
            $table->integer('follows')->nullable();
            $table->integer('unfollows')->nullable();
            $table->integer('followers_variation')->nullable();
            $table->integer('befriends')->nullable();
            $table->integer('unfriends')->nullable();
            $table->integer('total_followers')->nullable();
            $table->integer('total_friends')->nullable();
            $table->float('followers_followback_percent')->nullable();
            $table->float('user_followback_percent')->nullable();
            $table->float('friends_to_followers_ratio')->nullable();
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
        });

        Schema::create('scheduled_tweets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')->nullable();
            $table->string('tweet_content')->nullable();
            $table->string('schedule_time')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles');
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
