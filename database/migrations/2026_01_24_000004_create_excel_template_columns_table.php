<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_template_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->index()->constrained('excel_templates')->onDelete('cascade');
            $table->string('key');
            $table->string('label');
            $table->string('data_type')->default('string');
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['template_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_template_columns');
    }
};
