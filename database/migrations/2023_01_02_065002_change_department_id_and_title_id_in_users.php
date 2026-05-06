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
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE `users` MODIFY `department_id` INTEGER NULL');
            DB::statement('ALTER TABLE `users` MODIFY `title_id` INTEGER NULL');
            // $table->integer('department_id')->nullable()->change();
            // $table->integer('title_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->rollBack('title_id');
            // $table->rollBack('department_id');
        });
    }
};
