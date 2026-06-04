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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('peak_hours')->default('[9, 10, 11, 14, 15, 16]');
            $table->json('avoid_hours')->default('[12, 13, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8]');
            $table->unsignedSmallInteger('max_daily_tasks')->default(10);
            $table->unsignedSmallInteger('focus_block_minutes')->default(90);
            $table->unsignedSmallInteger('break_minutes')->default(15);
            $table->boolean('notifications_on')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
