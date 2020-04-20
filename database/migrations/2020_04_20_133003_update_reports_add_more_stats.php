<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateReportsAddMoreStats extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('followers', 'profile_total_followers');
            $table->renameColumn('unfollowers', 'follows');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->integer('unfollows')->after('follows');
        });

        DB::statement("ALTER TABLE reports MODIFY COLUMN followers_variation INTEGER AFTER unfollows");
        DB::statement("ALTER TABLE reports MODIFY COLUMN profile_total_followers INTEGER AFTER followers_variation");
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
