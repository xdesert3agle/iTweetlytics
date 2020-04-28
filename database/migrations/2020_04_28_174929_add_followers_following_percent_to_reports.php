<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowersFollowingPercentToReports extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('followback_percent', 'followers_followback_percent');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->float('user_followback_percent')->after('followers_followback_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
}
