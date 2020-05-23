<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToAllTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('befriends', function (Blueprint $table) {
            $table->string('description')->after('screen_name');
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->string('description')->after('screen_name');
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->string('description')->after('screen_name');
            $table->string('location')->after('profile_image_url');
            $table->dropForeign('profile_changes_report_id_foreign');
            $table->dropColumn('report_id');
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->string('description')->after('screen_name');
        });

        Schema::table('unfollows', function (Blueprint $table) {
            $table->string('location')->after('profile_image_url');
        });

        Schema::table('unfollows', function (Blueprint $table) {
            $table->string('description')->after('screen_name');
            $table->dropForeign('unfollows_report_id_foreign');
            $table->dropColumn('report_id');
        });

        Schema::table('unfriends', function (Blueprint $table) {
            $table->string('description')->after('screen_name');
            $table->dropColumn('follows_you');
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
