<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('failed_rows', function (Blueprint $table) {
            // Add new columns for improved tracking
            if (!Schema::hasColumn('failed_rows', 'row_number')) {
                $table->integer('row_number')->nullable()->after('task_id');
            }
            if (!Schema::hasColumn('failed_rows', 'data')) {
                $table->json('data')->nullable()->after('row_number');
            }
            if (!Schema::hasColumn('failed_rows', 'is_valid')) {
                $table->boolean('is_valid')->default(true)->after('data');
            }
            if (!Schema::hasColumn('failed_rows', 'error_messages')) {
                $table->json('error_messages')->nullable()->after('is_valid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('failed_rows', function (Blueprint $table) {
            $table->dropColumn(['row_number', 'data', 'is_valid', 'error_messages']);
        });
    }
};
