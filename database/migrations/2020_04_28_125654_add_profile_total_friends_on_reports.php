<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileTotalFriendsOnReports extends Migration {
    /**
     * Run the migrations.
     *r
     * @return void
     */
    public function up() {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('profile_total_followers', 'total_followers');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->integer('total_friends')->nullable()->after('total_followers');
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
