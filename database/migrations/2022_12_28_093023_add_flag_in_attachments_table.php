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
            $table->enum('flag', ['0', '1'])->default('0')->comment('0:task attachment, 1:comment attachment');
            $table->integer('createdBy')->default(0);
            $table->integer('updatedBy')->default(0);
            $table->integer('commentId')->constrained('task_comments')->cascadeOnDelete()->cascadeOnUpdate()->default(0);
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
            $table->dropColumn('flag');
            $table->dropColumn('createdBy');
            $table->dropColumn('updatedBy');
            $table->dropColumn('commentId');
        });
    }
};
