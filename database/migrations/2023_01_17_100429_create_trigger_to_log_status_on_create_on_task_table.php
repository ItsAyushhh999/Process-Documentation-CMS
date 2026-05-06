<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `trigger_for_task_status_logs_on_create`');
        DB::unprepared('
        CREATE TRIGGER trigger_for_task_status_logs_on_create
        AFTER INSERT
        ON
            `tasks` FOR EACH ROW
        BEGIN
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
                NEW.status,
                NEW.status,
                NEW.createdBy,
                NEW.createdBy,
                NEW.created_at,
                NEW.created_at
            );
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

    }
};
