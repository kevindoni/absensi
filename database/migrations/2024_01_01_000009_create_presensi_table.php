<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal_mengajar')->onDelete('cascade');
            $table->date('tanggal');
            $table->datetime('waktu_masuk');
            $table->datetime('waktu_keluar')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'tidak_hadir'])->default('hadir');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
