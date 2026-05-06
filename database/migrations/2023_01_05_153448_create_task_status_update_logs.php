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
        Schema::create('task_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taskId')->constrained('tasks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('previousStatus', ['0', '1', '2', '3', '4', '5', '6', '7'])->comment('0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing, 4:reviewed, 5:Completed, 6:Closed, 7:created')->default('7');
            $table->enum('currentStatus', ['0', '1', '2', '3', '4', '5', '6', '7'])->comment('0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing, 4:reviewed, 5:Completed, 6:Closed, 7:created')->default('7');
            $table->foreignId('createdBy')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->default(0);
            $table->integer('updatedBy')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_status_logs');

    }
};
