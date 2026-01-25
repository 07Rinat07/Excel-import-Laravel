<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->json('column_map')->nullable()->after('template_id');
            $table->unsignedInteger('total_rows')->default(0)->after('column_map');
            $table->unsignedInteger('imported_rows')->default(0)->after('total_rows');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['column_map', 'total_rows', 'imported_rows']);
        });
    }
};
