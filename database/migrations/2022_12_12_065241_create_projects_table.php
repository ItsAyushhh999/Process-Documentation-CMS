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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            $table->string('url')->comment('project url');
            $table->string('sub_projects')->nullable()->default(0);
            $table->string('development_pipeline')->nullable();
            $table->string('staging_pipeline')->nullable();
            $table->string('production_Pipeline')->nullable();
            $table->enum('branch', ['main', 'production', 'stagging', 'development']);
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
        Schema::dropIfExists('projects');
    }
};
