<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('parent_task_id')->nullable();
            // $table->foreign('parent_task_id')->references('id')->on('tasks')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'snoozed', 'completed', 'skipped'])
                  ->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->unsignedInteger('estimated_minutes')->nullable();
            $table->unsignedInteger('actual_minutes')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('ai_rank')->default(0);
            $table->float('ai_score', 5, 2)->default(0);
            $table->json('ai_reasoning')->nullable(); 
            $table->index(['user_id', 'status', 'due_at']);
            $table->index(['user_id', 'ai_rank']);
            // add FK after id exists
            // $table->foreign('parent_task_id')->references('id')->on('tasks')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('parent_task_id')
                ->references('id')
                ->on('tasks')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
        });

        Schema::dropIfExists('tasks');
    }
};
