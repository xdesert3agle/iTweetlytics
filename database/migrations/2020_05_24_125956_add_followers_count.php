<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowersCount extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('befriends', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->integer('followers_count')->after('description')->nullable();
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->integer('followers_count')->after('description')->nullable();
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('unfollows', function (Blueprint $table) {
            $table->string('location')->nullable()->change();
            $table->string('description')->nullable()->change();
            $table->integer('followers_count')->after('description')->nullable();
        });

        Schema::table('unfriends', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->integer('followers_count')->after('description')->nullable();
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
