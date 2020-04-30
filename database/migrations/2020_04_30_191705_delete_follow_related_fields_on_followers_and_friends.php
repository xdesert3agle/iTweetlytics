<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteFollowRelatedFieldsOnFollowersAndFriends extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('friends', function (Blueprint $table) {
            $table->dropColumn('is_following');
        });

        Schema::table('followers', function (Blueprint $table) {
            $table->dropColumn('following');
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
