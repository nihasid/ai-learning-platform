<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->string('avatar_color', 16)->default('#38bdf8');
            $table->boolean('audio_guidance_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('learning_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('domain');
            $table->text('prompt');
            $table->string('audio_prompt')->nullable();
            $table->unsignedTinyInteger('age_min')->default(2);
            $table->unsignedTinyInteger('age_max')->default(8);
            $table->string('button_color', 16)->default('#f97316');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('child_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('child_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('learning_activity_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('started');
            $table->unsignedInteger('attempts')->default(1);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['child_profile_id', 'learning_activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_progress');
        Schema::dropIfExists('learning_activities');
        Schema::dropIfExists('child_profiles');
    }
};
