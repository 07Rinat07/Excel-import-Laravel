<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('task_id')->nullable()->index()->constrained('tasks');
            $table->foreignId('template_id')->nullable()->index()->constrained('excel_templates');
            $table->unsignedInteger('row_index')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['template_id']);
            $table->dropColumn(['task_id', 'template_id', 'row_index']);
        });
    }
};
