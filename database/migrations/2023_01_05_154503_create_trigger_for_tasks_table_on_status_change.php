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
        DB::unprepared('DROP TRIGGER IF EXISTS `trigger_for_task_status_logs`');
        DB::unprepared('
        CREATE TRIGGER trigger_for_task_status_logs
        AFTER UPDATE
        ON
            `tasks` FOR EACH ROW
        BEGIN
            IF(NEW.status != OLD.status) THEN
                INSERT INTO task_status_logs(
                    `taskId`,
                    `previousStatus`,
                    `currentStatus`,
                    `updatedBy`,
                    `createdBy`,
                    `updated_at`,
                    `created_at`
                )
                VALUES(
                    NEW.id,
                    OLD.status,
                    NEW.status,
                    NEW.updatedBy,
                    NEW.updatedBy,
                    NEW.updated_at,
                    NEW.updated_at
                );
            END IF;
        END
        ');
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
