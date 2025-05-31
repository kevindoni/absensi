<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel migrations jika belum ada
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        }

        // Daftar semua migrasi yang sudah ada
        $migrations = [
            ['migration' => '2024_01_01_000001_create_academic_years_table', 'batch' => 1],
            ['migration' => '2024_01_01_000002_create_admins_table', 'batch' => 1],
            ['migration' => '2024_01_01_000003_create_gurus_table', 'batch' => 1],
            ['migration' => '2024_01_01_000004_create_kelas_table', 'batch' => 1],
            ['migration' => '2024_01_01_000005_create_siswas_table', 'batch' => 1],
            ['migration' => '2024_01_01_000006_create_orangtuas_table', 'batch' => 1],
            ['migration' => '2024_01_01_000007_create_pelajaran_table', 'batch' => 1],
            ['migration' => '2024_01_01_000008_create_jadwal_mengajar_table', 'batch' => 1],
            ['migration' => '2024_01_01_000009_create_presensi_table', 'batch' => 1],
            ['migration' => '2024_01_01_000010_create_absensis_table', 'batch' => 1],            
            ['migration' => '2024_01_01_000011_create_jurnal_mengajar_table', 'batch' => 1],
            ['migration' => '2024_01_01_000012_create_settings_table', 'batch' => 1],            
            ['migration' => '2024_01_01_000013_create_notifications_table', 'batch' => 1],            
            ['migration' => '2024_01_01_000014_create_pesan_table', 'batch' => 1],
            ['migration' => '2024_01_01_000015_create_izin_table', 'batch' => 1],
            ['migration' => '2024_01_01_000016_create_sessions_table', 'batch' => 1],
            ['migration' => '2024_01_01_000017_create_jobs_table', 'batch' => 1],
            ['migration' => '2024_01_01_000018_create_failed_jobs_table', 'batch' => 1],
            ['migration' => '2024_01_01_000019_create_password_reset_tokens_table', 'batch' => 1],            
            ['migration' => '2024_01_01_000020_create_absensi_details_table', 'batch' => 1],
            ['migration' => '2024_01_01_000021_create_jurusan_table', 'batch' => 1],
            ['migration' => '2024_01_01_000022_create_personal_access_tokens_table', 'batch' => 1],
            ['migration' => '2024_01_01_000023_update_siswa_orangtua_relationship', 'batch' => 1],
        ];

        DB::table('migrations')->insert($migrations);
    }

    public function down(): void
    {
        Schema::dropIfExists('migrations');
    }
};
