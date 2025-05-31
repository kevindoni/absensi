<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // Fix orangtuas table
        Schema::table('orangtuas', function (Blueprint $table) {
            // Update hubungan enum settings
            DB::statement("ALTER TABLE orangtuas MODIFY COLUMN hubungan ENUM('Ayah', 'Ibu', 'Wali') NULL");
        });

        // We already have QR fields in siswas table from migration 005

        // Add indexes to absensis table (these are safe to add even if they exist)
        Schema::table('absensis', function (Blueprint $table) {
            // Create indexes if they don't exist
            if (!Schema::hasIndex('absensis', 'absensi_unique_jadwal')) {
                $table->unique(['siswa_id', 'jadwal_id', 'tanggal'], 'absensi_unique_jadwal');
            }
            if (!Schema::hasIndex('absensis', 'absensis_siswa_id_index')) {
                $table->index('siswa_id');
            }
            if (!Schema::hasIndex('absensis', 'absensis_tanggal_index')) {
                $table->index('tanggal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        // Fix orangtuas table
        Schema::table('orangtuas', function (Blueprint $table) {
            // Update hubungan enum settings back to not null
            DB::statement("ALTER TABLE orangtuas MODIFY COLUMN hubungan ENUM('Ayah', 'Ibu', 'Wali') NOT NULL");
        });

        // Remove indexes from absensis (if they exist)
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensi_unique_jadwal');
            $table->dropIndex(['siswa_id']);
            $table->dropIndex(['tanggal']);
        });

        // Revert siswas table
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_generated_at']);
        });

        // Revert orangtuas table
        Schema::table('orangtuas', function (Blueprint $table) {
            $table->renameColumn('no_telp', 'no_hp');
            $table->dropColumn('hubungan');
        });

        // Revert kelas table
        Schema::table('kelas', function (Blueprint $table) {
            $table->integer('tingkat')->change();
            $table->foreignId('jurusan')->constrained('jurusan');
            $table->foreignId('academic_year_id')->constrained();
        });

        // Revert gurus table
        Schema::table('gurus', function (Blueprint $table) {
            $table->renameColumn('no_telp', 'no_hp');
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};
