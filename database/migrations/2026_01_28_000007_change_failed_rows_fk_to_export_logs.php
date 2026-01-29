<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('failed_rows', function (Blueprint $table) {
            // Drop the old foreign key that references tasks table
            try {
                $table->dropForeign('failed_rows_task_id_foreign');
            } catch (\Throwable $e) {
                // Foreign key might not exist, continue
            }
        });

        // Add the new foreign key that references export_logs table
        Schema::table('failed_rows', function (Blueprint $table) {
            $table->foreign('task_id')
                ->references('id')
                ->on('export_logs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('failed_rows', function (Blueprint $table) {
            try {
                $table->dropForeign('failed_rows_task_id_foreign');
            } catch (\Throwable $e) {
                // continue
            }
        });
    }
};
