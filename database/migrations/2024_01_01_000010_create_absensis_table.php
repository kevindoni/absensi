<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal_mengajar')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained()->onDelete('set null');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['siswa_id', 'jadwal_id', 'tanggal'], 'absensi_unique_jadwal');
            $table->index('siswa_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
