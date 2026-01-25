<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('sheet_name')->nullable()->after('row_index');
            $table->unsignedSmallInteger('sheet_index')->nullable()->after('sheet_name');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['sheet_name', 'sheet_index']);
        });
    }
};
