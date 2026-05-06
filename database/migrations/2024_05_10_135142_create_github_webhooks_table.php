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
        Schema::create('github_webhooks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pull_request_id')->nullable()->index();
            $table->string('pull_request_url')->nullable();
            $table->string('pull_request_title')->nullable();
            $table->string('pull_request_sender_username')->nullable()->index();
            $table->string('pull_request_sender_url')->nullable();
            $table->longText('pull_request_comment')->nullable();

            $table->string('repository_name')->nullable();
            $table->string('repository_full_name')->nullable();
            $table->string('repository_url')->nullable();

            $table->string('sender_username')->nullable()->index();
            $table->string('sender_url')->nullable();
            $table->enum('status', ['ATTACHED', 'UN-ATTACHED', 'RESOLVED'])->default('UN-ATTACHED');

            $table->foreignId('task_id')->nullable()
                ->constrained('tasks')->cascadeOnDelete();
            $table->unsignedBigInteger('notified')->default(0);
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->cascadeOnDelete();

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
        Schema::dropIfExists('github_webhooks');
    }
};
