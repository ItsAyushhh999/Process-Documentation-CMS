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
        Schema::table('task_statuses', function (Blueprint $table) {
            $table->enum('status', ['0', '1'])->default('1')->comment('0:Inactive, 1:Active')->after('id');
            // change value from enum to integer
            $table->unsignedInteger('value')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_statuses', function (Blueprint $table) {
            $table->dropColumn('status');
            DB::statement("ALTER TABLE `task_statuses`
            CHANGE `value` `value`
            enum('0','1','2','3','4','5','6','7','8','9','10','11', '12', '13', '14', '15', '16', '17', '18', '19', '20')
            COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT '0'
        ");
        });
    }
};
