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
        Schema::table('izin', function (Blueprint $table) {
            // Drop existing columns we want to modify
            $table->dropColumn(['tanggal', 'jenis', 'status', 'bukti']);

            // Add new columns
            $table->date('tanggal_mulai')->after('siswa_id');
            $table->date('tanggal_selesai')->after('tanggal_mulai');
            $table->enum('jenis', ['sakit', 'izin'])->after('tanggal_selesai');
            $table->string('bukti_file')->nullable()->after('keterangan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('bukti_file');
            $table->text('catatan_admin')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->constrained('admins')->onDelete('set null')->after('catatan_admin');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Add cascade delete for student relationship
            $table->dropForeign(['siswa_id']);
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin', function (Blueprint $table) {
            // Remove new columns
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'tanggal_mulai',
                'tanggal_selesai',
                'bukti_file',
                'catatan_admin',
                'approved_by',
                'approved_at'
            ]);

            // Restore old columns
            $table->date('tanggal')->after('siswa_id');
            $table->string('jenis')->after('tanggal');
            $table->string('status')->default('pending')->after('keterangan');
            $table->string('bukti')->nullable()->after('keterangan');

            // Restore original foreign key
            $table->dropForeign(['siswa_id']);
            $table->foreign('siswa_id')->references('id')->on('siswas');
        });
    }
};
