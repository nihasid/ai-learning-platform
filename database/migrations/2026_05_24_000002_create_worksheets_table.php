<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worksheets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('subject');
            $table->string('age_group');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('child_worksheet_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('worksheet_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('assigned');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['child_profile_id', 'worksheet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_worksheet_assignments');
        Schema::dropIfExists('worksheets');
    }
};
