<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateProfileChangesAddMoreFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement("ALTER TABLE profile_changes MODIFY COLUMN action INTEGER AFTER report_id");

        Schema::table('profile_changes', function (Blueprint $table) {
            $table->renameColumn('twitter_user_id', 'id_str');
        });

        Schema::table('profile_changes', function (Blueprint $table) {
            $table->string('name')->after('id_str');
            $table->string('profile_image_url')->after('screen_name');
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
