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
        Schema::create('task_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taskId')->constrained('tasks')->cascadeOnDelete()->cascadeOnUpdate()->default(0);
            $table->enum('action', ['0', '1', '2'])->comment('0: Added, 1: Removed, 2: Updated');
            $table->enum('property', ['0', '1', '2', '3', '4'])->comment('0: Assignee, 1: Reviewer, 2: Deadline, 3: Priority, 4:taskTypes');
            $table->integer('oldId')->nullable();
            $table->integer('newId')->nullable();
            $table->string('oldValue')->nullable();
            $table->string('newValue')->nullable();
            $table->integer('createdBy');
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
        Schema::dropIfExists('task_activity_logs');
    }
};
