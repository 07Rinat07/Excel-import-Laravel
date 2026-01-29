<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_id')->nullable()->unique(); // Domain UUID (nullable for import tasks)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source_type'); // 'export' or 'import'
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('format');
            $table->string('status');
            $table->string('file_name')->nullable();
            $table->integer('columns_count')->default(0);
            $table->integer('rows_processed')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
};
