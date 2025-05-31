<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();              
            $table->string('username')->unique();
            $table->string('password');
            $table->string('nip', 20)->nullable();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_telp')->nullable();
            $table->text('alamat');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->rememberToken();
            $table->timestamps();

            // MySQL-compatible way to make NIP unique only when not null
            $table->unique('nip', 'gurus_nip_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
