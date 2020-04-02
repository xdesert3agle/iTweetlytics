<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitterFieldsOnUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();

            $table->string('id')->change();
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('screen_name')->nullable()->after('email');
            $table->string('location')->nullable()->after('screen_name');
            $table->string('description')->nullable()->after('location');
            $table->boolean('protected')->nullable()->after('description');
            $table->integer('followers_count')->nullable()->after('protected');
            $table->integer('friends_count')->nullable()->after('followers_count');
            $table->integer('listed_count')->nullable()->after('friends_count');
            $table->integer('favourites_count')->nullable()->after('listed_count');
            $table->string('time_zone')->nullable()->after('favourites_count');
            $table->boolean('geo_enabled')->nullable()->after('time_zone');
            $table->boolean('verified')->nullable()->after('geo_enabled');
            $table->integer('statuses_count')->nullable()->after('verified');
            $table->string('profile_background_color')->nullable()->after('statuses_count');
            $table->string('profile_image_url')->nullable()->after('profile_background_color');
            $table->string('profile_banner_url')->nullable()->after('profile_image_url');
            $table->string('profile_link_color')->nullable()->after('profile_banner_url');
            $table->string('lang')->nullable()->after('profile_link_color');
            $table->boolean('suspended')->nullable()->after('lang');
            $table->string('oauth_token')->nullable()->after('suspended');
            $table->string('oauth_token_secret')->nullable()->after('oauth_token');

            $table->primary('id');
            $table->dropColumn('password');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('user', function (Blueprint $table) {
            //
        });
    }
}
