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
        Schema::create('project_deployment_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('stage', ['0', '1', '2'])->comment('0:development, 1:staging, 2:production');
            $table->enum('account_identifier', ['0', '1'])->comment('0: voxmg, 1:voxships');
            $table->string('role_session_name');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->unique(['project_id', 'stage']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_deployment_mappings');
    }
};
