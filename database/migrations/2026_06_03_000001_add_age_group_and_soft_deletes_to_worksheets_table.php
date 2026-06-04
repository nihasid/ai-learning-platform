<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worksheets', function (Blueprint $table) {
            if (! Schema::hasColumn('worksheets', 'age_group')) {
                $table->string('age_group')->default('4-5')->after('subject');
            }

            if (! Schema::hasColumn('worksheets', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('worksheets', function (Blueprint $table) {
            if (Schema::hasColumn('worksheets', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('worksheets', 'age_group')) {
                $table->dropColumn('age_group');
            }
        });
    }
};
