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
        Schema::create('merges_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taskId')->constrained('tasks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('projectId')->constrained('projects')->cascadeOnDelete()->cascadeOnUpdate()->default(0);
            $table->integer('pr_no');
            $table->integer('merge_by');
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
        Schema::dropIfExists('merges_logs');
    }
};
