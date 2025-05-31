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
        Schema::table('absensi_details', function (Blueprint $table) {
            // Drop existing foreign key and status column
            $table->dropForeign(['absensi_id']);
            $table->dropColumn('status');

            // Add cascade delete and enum status
            $table->foreign('absensi_id')->references('id')->on('absensis')->onDelete('cascade');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir')->after('siswa_id');
            
            // Rename scan_time to waktu_absen for consistency
            $table->renameColumn('scan_time', 'waktu_absen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_details', function (Blueprint $table) {
            // Remove cascade delete and enum status
            $table->dropForeign(['absensi_id']);
            $table->dropColumn('status');
            
            // Add back original columns
            $table->foreign('absensi_id')->references('id')->on('absensis');
            $table->string('status')->after('siswa_id');

            // Rename waktu_absen back to scan_time
            $table->renameColumn('waktu_absen', 'scan_time');
        });
    }
};
