<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowsYouToFriends extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('friends', function (Blueprint $table) {
            $table->boolean('follows_you')->nullable()->after('location');
        });

        Schema::table('unfriends', function (Blueprint $table) {
            $table->boolean('follows_you')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('friends', function (Blueprint $table) {
            //
        });
    }
}
