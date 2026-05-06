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
            Illuminate\Support\Facades\DB::statement("ALTER TABLE attachments MODIFY flag ENUM('0','1','2') DEFAULT '0'");
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
            Illuminate\Support\Facades\DB::statement("ALTER TABLE attachments MODIFY flag ENUM('0','1','2') DEFAULT '0'");
        });
    }
};
