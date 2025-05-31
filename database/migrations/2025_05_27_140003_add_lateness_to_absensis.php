<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Add new status values for lateness
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'terlambat', 'izin', 'sakit', 'alpha') DEFAULT 'hadir'");
            
            // Add columns for lateness tracking
            $table->integer('minutes_late')->nullable()->after('status');
            $table->boolean('is_valid_attendance')->default(true)->after('minutes_late');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Remove lateness columns
            $table->dropColumn(['minutes_late', 'is_valid_attendance']);
            
            // Revert status enum
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha') DEFAULT 'hadir'");
            
            // Update any 'terlambat' records back to 'hadir'
            DB::statement("UPDATE absensis SET status = 'hadir' WHERE status = 'terlambat'");
        });
    }
};
