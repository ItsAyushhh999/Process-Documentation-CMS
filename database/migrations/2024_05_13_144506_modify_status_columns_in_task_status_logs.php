<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_status_logs', function (Blueprint $table) {
            //alter currentStatus and previousStatus from enum to integer
            $table->unsignedInteger('currentStatus')->default(7)->change();
            $table->unsignedInteger('previousStatus')->default(7)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_status_logs', function (Blueprint $table) {
            //alter currentStatus and previousStatus from integer to enum
            DB::statement("ALTER TABLE `task_status_logs`
                CHANGE `previousStatus` `previousStatus`
                enum('0','1','2','3','4','5','6','7','8','9','10','11')
                COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT '7'
                COMMENT '0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing, 4:reviewed, 5:Completed,
                6:Closed, 7:created, 8: staging - Ready to upload, 9: staging - Upload Completed,
                 10: Live - Ready to Upload, 11: Live - Upload Completed'
            ");

            DB::statement("ALTER TABLE `task_status_logs`
                CHANGE `currentStatus` `currentStatus`
                enum('0','1','2','3','4','5','6','7','8','9','10','11')
                COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT '7'
                COMMENT '0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing, 4:reviewed, 5:Completed,
                6:Closed, 7:created, 8: staging - Ready to upload, 9: staging - Upload Completed,
                 10: Live - Ready to Upload, 11: Live - Upload Completed'
            ");
        });
    }
};
