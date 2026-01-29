<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('excel_template_columns', function (Blueprint $table) {
            $table->json('validation_rules')->nullable()->after('is_required');
        });
    }

    public function down(): void
    {
        Schema::table('excel_template_columns', function (Blueprint $table) {
            $table->dropColumn('validation_rules');
        });
    }
};
