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
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('dir');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('assignedAt')->nullable();
            $table->integer('assignedBy')->default(0);
            $table->timestamp('completedAt')->nullable()->comment('time when assigned for review');
            $table->integer('completedBy')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('dir');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('assignedAt');
            $table->dropColumn('assignedBy');
            $table->dropColumn('completedAt');
            $table->dropColumn('completedBy');
        });
    }
};
