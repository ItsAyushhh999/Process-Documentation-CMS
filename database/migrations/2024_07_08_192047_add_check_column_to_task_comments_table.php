<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckColumnToTaskCommentsTable extends Migration
{
    public function up()
    {
        Schema::table('task_comments', function (Blueprint $table) {
            $table->enum('check', ['0', '1'])->default('0')->comment('0:unChecked, 1:Checked');
            $table->integer('checkedBy')->nullable();
        });
    }

    public function down()
    {
        Schema::table('task_comments', function (Blueprint $table) {
            $table->dropColumn('check');
            $table->dropColumn('checkedBy');
        });
    }
}
