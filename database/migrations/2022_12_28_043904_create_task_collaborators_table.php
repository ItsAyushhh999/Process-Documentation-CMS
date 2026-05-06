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
        Schema::create('task_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taskId')->constrained('tasks')->cascadeOnDelete()->cascadeOnUpdate()->default(0);
            $table->foreignId('collaborator')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->default(0);
            $table->enum('flag', ['0', '1'])->default('0')->comment('0:Assignee, 1:Reviewer');
            $table->integer('createdBy')->default(0);
            $table->integer('updatedBy')->default(0);
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['0', '1', '2', '3', '4', '5', '6', '7'])->comment('0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing, 4:reviewed, 5:Completed, 6:Closed, 7:created')->default('7');
            $table->integer('createdBy')->default(0);
            $table->integer('updatedBy')->default(0);

            $table->dropColumn('assignee');
            $table->dropColumn('reviewer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_collaborators');

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('assignee')->nullable();
            $table->string('reviewer')->nullable();
            $table->dropColumn('status');
            $table->dropColumn('createdBy');
            $table->dropColumn('updatedBy');
        });
    }
};
