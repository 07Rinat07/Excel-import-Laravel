<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->index()->constrained('types');
            $table->foreignId('template_id')->nullable()->index()->constrained('excel_templates');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropForeign(['template_id']);
            $table->dropColumn(['type_id', 'template_id']);
        });
    }
};
