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
            $table->string('screen_name')->nullable()->after('name');
            $table->string('location')->nullable()->after('screen_name');
            $table->string('description')->nullable()->after('location');
            $table->string('protected')->nullable()->after('description');
            $table->string('followers_count')->nullable()->after('protected');
            $table->string('friends_count')->nullable()->after('followers_count');
            $table->string('listed_count')->nullable()->after('friends_count');
            $table->string('favourites_count')->nullable()->after('listed_count');
            $table->string('time_zone')->nullable()->after('favourites_count');
            $table->string('verified')->nullable()->after('time_zone');
            $table->string('lang')->nullable()->after('verified');
            $table->string('access_token')->nullable()->after('lang');

            $table->dropColumn('password');
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
