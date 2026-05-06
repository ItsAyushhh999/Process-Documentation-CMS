<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            DB::statement("ALTER TABLE `tasks` CHANGE `status` `status` enum('0','1','2','3','4','5','6','7','8')
            COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT '7'
            COMMENT '0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing, 4:reviewed, 5:Completed, 6:Closed, 7:created, 8: uploaded to production'
            AFTER `deadline`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
