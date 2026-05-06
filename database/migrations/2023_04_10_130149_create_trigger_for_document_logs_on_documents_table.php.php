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
        DB::unprepared('DROP TRIGGER IF EXISTS `trigger_for_document_logs`');
        DB::unprepared('
        CREATE TRIGGER trigger_for_document_logs
        AFTER UPDATE
        ON
            `documents` FOR EACH ROW
        BEGIN
            INSERT INTO document_logs(
                `documentId`,
                `updatedBy`,
                `createdBy`,
                `updated_at`,
                `created_at`
            )
            VALUES(
                NEW.id,
                NEW.updatedBy,
                NEW.createdBy,
                NEW.updated_at,
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
        //
    }
};
