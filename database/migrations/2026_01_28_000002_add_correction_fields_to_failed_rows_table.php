<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('failed_rows', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('message');
            $table->json('corrected_data')->nullable()->after('errors');
            $table->boolean('is_corrected')->default(false)->after('corrected_data');
        });
    }

    public function down(): void
    {
        Schema::table('failed_rows', function (Blueprint $table) {
            $table->dropColumn(['errors', 'corrected_data', 'is_corrected']);
        });
    }
};
