<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if migrations table exists and has content
        if (DB::table('migrations')->count() == 0) {
            // Mark all our migrations as run
            $migrations = [
                ['migration' => '2024_01_01_000001_create_academic_years_table', 'batch' => 1],
                ['migration' => '2024_01_01_000002_create_admins_table', 'batch' => 1],
                ['migration' => '2024_01_01_000003_create_orangtuas_table', 'batch' => 1],
                ['migration' => '2024_01_01_000004_create_gurus_table', 'batch' => 1],
                ['migration' => '2024_01_01_000005_create_jurusan_table', 'batch' => 1],
                ['migration' => '2024_01_01_000006_create_kelas_table', 'batch' => 1],
                ['migration' => '2024_01_01_000007_create_siswas_table', 'batch' => 1],
                ['migration' => '2024_01_01_000008_create_pelajaran_table', 'batch' => 1],
                ['migration' => '2024_01_01_000009_create_jadwal_mengajar_table', 'batch' => 1],
                ['migration' => '2024_01_01_000010_create_presensi_table', 'batch' => 1],
                ['migration' => '2024_01_01_000011_create_absensis_table', 'batch' => 1],
                ['migration' => '2024_01_01_000012_create_absensi_details_table', 'batch' => 1],
                ['migration' => '2024_01_01_000013_create_izin_table', 'batch' => 1],
                ['migration' => '2024_01_01_000014_create_jurnal_mengajar_table', 'batch' => 1],
                ['migration' => '2024_01_01_000016_create_pesans_table', 'batch' => 1],
                ['migration' => '2024_01_01_000017_create_settings_table', 'batch' => 1],
                ['migration' => '2024_01_01_000018_sync_database_structure', 'batch' => 1],
            ];
            
            DB::table('migrations')->insert($migrations);
        }
    }

    public function down(): void
    {
        // Nothing to do here
    }
};
