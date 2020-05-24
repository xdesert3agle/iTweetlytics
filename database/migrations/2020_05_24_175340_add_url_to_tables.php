<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('befriends', function (Blueprint $table) {
            $table->string('url')->after('location')->nullable();
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->string('url')->after('location')->nullable();
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->string('url')->after('location')->nullable();
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->string('url')->after('location')->nullable();
        });

        Schema::table('unfollows', function (Blueprint $table) {
            $table->string('url')->after('location')->nullable();
        });

        Schema::table('unfriends', function (Blueprint $table) {
            $table->string('url')->after('location')->nullable();
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
