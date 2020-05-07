<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCursorsDefaultValue extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('twitter_profiles', function (Blueprint $table) {
            $table->string('next_followers_cursor')->default(-1)->change();
            $table->string('next_friends_cursor')->default(-1)->change();
            $table->dropColumn('previous_followers_cursor');
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
