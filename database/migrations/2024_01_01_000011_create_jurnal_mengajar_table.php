<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_mengajar', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('jadwal_id')->constrained('jadwal_mengajar')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained()->onDelete('cascade');
            $table->string('materi');
            $table->text('kegiatan');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['tanggal', 'jadwal_id', 'guru_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_mengajar');
    }
};
