<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFollowersAddMoreFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('followers', function (Blueprint $table) {
            $table->renameColumn('twitter_user_id', 'id_str');
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->string('name')->after('id_str');
            $table->integer('followers_count')->after('screen_name');
            $table->string('location')->after('followers_count');
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
