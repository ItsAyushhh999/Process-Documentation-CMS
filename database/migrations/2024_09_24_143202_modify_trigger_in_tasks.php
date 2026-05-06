<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_collaborator_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_collaborator_delete`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_types_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_types_delete`');
        DB::unprepared('DROP TRIGGER IF EXISTS `log_task_update_after`');

        $path = database_path('migrations/sql/triggers/task_collaborator/insert.sql');
        $saleTrigger = File::get($path);
        DB::unprepared($saleTrigger);

        $path = database_path('migrations/sql/triggers/task_collaborator/delete.sql');
        $saleTrigger = File::get($path);
        DB::unprepared($saleTrigger);

        $path = database_path('migrations/sql/triggers/task_types/insert.sql');
        $saleTrigger = File::get($path);
        DB::unprepared($saleTrigger);

        $path = database_path('migrations/sql/triggers/task_types/delete.sql');
        $saleTrigger = File::get($path);
        DB::unprepared($saleTrigger);

        $path = database_path('migrations/sql/triggers/tasks/update.sql');
        $saleTrigger = File::get($path);
        DB::unprepared($saleTrigger);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_collaborator_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_collaborator_delete`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_types_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_task_types_delete`');
        DB::unprepared('DROP TRIGGER IF EXISTS `log_task_update_after`');
    }
};
