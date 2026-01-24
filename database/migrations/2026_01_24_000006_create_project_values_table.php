<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->index()->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_column_id')->index()->constrained('excel_template_columns')->onDelete('cascade');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'template_column_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_values');
    }
};
