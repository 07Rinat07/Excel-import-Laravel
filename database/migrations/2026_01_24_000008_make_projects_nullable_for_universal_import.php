<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->date('created_at_time')->nullable()->change();
            $table->date('contracted_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->date('created_at_time')->nullable(false)->change();
            $table->date('contracted_at')->nullable(false)->change();
        });
    }
};
