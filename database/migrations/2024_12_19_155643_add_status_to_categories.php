<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Adding an integer column with default value 0 for 'inactive' and 1 for 'active'
            $table->enum('status', ['0', '1'])->default('0')->comment('0: Inactive, 1: Active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Removing the 'status' column in case of rollback
            $table->dropColumn('status');
        });
    }
}
