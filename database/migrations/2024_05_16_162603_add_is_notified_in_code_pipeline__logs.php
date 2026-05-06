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
        Schema::table('code_pipeline_Logs', function (Blueprint $table) {
            $table->boolean('is_notified')->default(false);
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->string('stage_name')->comment('1: development, 2: production, 3: staging')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('code_pipeline_Logs', function (Blueprint $table) {
            $table->dropColumn(['is_notified', 'stage_name', 'updated_by']);
        });
    }
};
