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
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('priority', ['0', '1', '2'])->comment('0:Normal, 1:High, 2:Urgent')->default('0');
            $table->string('assignee')->nullable();
            $table->string('reviewer')->nullable();
            $table->timestamp('deadline')->nullable();
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
            $table->dropColumn('priority');
            $table->dropColumn('assignee');
            $table->dropColumn('reviewer');
            $table->dropColumn('deadline');
        });
    }
};
