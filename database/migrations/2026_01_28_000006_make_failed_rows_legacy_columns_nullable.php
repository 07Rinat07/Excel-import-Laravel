<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('failed_rows', function (Blueprint $table) {
            // Make old columns nullable for backward compatibility
            $table->string('key')->nullable()->change();
            $table->string('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('failed_rows', function (Blueprint $table) {
            $table->string('key')->nullable(false)->change();
            $table->string('message')->nullable(false)->change();
        });
    }
};
