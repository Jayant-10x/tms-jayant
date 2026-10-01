<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();

            // Relates the comment to a specific task
            $table->foreignId('prt_id')
                ->constrained('project_tasks', 'prt_id')
                ->cascadeOnDelete();

            // Relates the comment to the user who wrote it
            $table->foreignId('user_id')
                ->constrained('admin_users', 'adm_id')
                ->cascadeOnDelete();

            // The actual message content
            $table->text('comment');

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('task_comments');
    }
};
