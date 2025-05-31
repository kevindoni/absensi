<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained()->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained()->onDelete('cascade');
            $table->foreignId('pelajaran_id')->constrained('pelajaran')->onDelete('cascade');
            $table->integer('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('jam_ke')->nullable();
            $table->text('jam_ke_list')->nullable();
            $table->timestamps();

            $table->unique(['kelas_id', 'hari', 'jam_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mengajar');
    }
};
