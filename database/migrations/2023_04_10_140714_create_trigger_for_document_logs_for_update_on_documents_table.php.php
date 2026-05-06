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
        DB::unprepared('DROP TRIGGER IF EXISTS `trigger_for_document_logs_on_create`');
        DB::unprepared('
        CREATE TRIGGER trigger_for_document_logs_on_create
        AFTER INSERT
        ON
            `documents` FOR EACH ROW
        BEGIN
            INSERT INTO document_logs(
                `documentId`,
                `createdBy`,
                `created_at`
            )
            VALUES(
                NEW.id,
                NEW.createdBy,
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
