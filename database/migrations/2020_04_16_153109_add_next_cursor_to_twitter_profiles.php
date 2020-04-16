<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNextCursorToTwitterProfiles extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('twitter_profiles', function (Blueprint $table) {
            $table->string('previous_followers_cursor', 12)->nullable()->after('suspended');
            $table->string('next_followers_cursor', 12)->nullable()->after('previous_followers_cursor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('twitter_profiles', function (Blueprint $table) {
            //
        });
    }
}
