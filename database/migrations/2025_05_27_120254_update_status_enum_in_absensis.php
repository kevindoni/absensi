<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration modifies the enum values of the status column in absensis table
     * to ensure both 'alpha' and 'alpa' spellings are supported.
     */
    public function up(): void
    {
        // First update all 'alpa' values to 'alpha' for consistency
        DB::statement("UPDATE absensis SET status = 'alpha' WHERE status = 'alpa'");
        
        // Then modify the enum to include both spellings if needed in the future
        DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha', 'alpa') DEFAULT 'hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha') DEFAULT 'hadir'");
    }
};
