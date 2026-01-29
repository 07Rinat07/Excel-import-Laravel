<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'name')) {
                $table->string('name')->nullable()->after('file_id');
            }
            if (! Schema::hasColumn('tasks', 'mapping')) {
                $table->json('mapping')->nullable()->after('column_map');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'mapping')) {
                $table->dropColumn('mapping');
            }
            if (Schema::hasColumn('tasks', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
