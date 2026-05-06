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
        Schema::create('code_pipeline_Logs', function (Blueprint $table) {
            $table->id();
            $table->string('created_by');
            $table->string('pull_request')->nullable();
            $table->string('summary')->nullable();
            $table->integer('task_id');
            $table->string('project_name');
            $table->string('deploy')->comment('Approved Or Rejected');
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
        Schema::dropIfExists('code_pipeline_Logs');
    }
};
