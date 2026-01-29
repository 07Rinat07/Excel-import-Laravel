<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the old structure for backward compatibility
        // task_id will reference either tasks or export_logs table
    }

    public function down(): void
    {
        // No-op for down migration
    }
};
