<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_logs', function (Blueprint $table) {
            // Fields now added in main create table migration
            // This is a no-op to maintain migration history
        });
    }

    public function down(): void
    {
        Schema::table('export_logs', function (Blueprint $table) {
            $table->dropColumn(['rows_processed', 'failure_reason']);
        });
    }
};
