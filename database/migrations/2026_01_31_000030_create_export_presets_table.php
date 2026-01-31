<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->string('format');
            $table->json('columns');
            $table->json('labels')->nullable();
            $table->string('sheet_name')->nullable();
            $table->integer('sheet_index')->nullable();
            $table->unsignedBigInteger('filter_user_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_presets');
    }
};
