<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop the existing foreign key in orangtuas table
        Schema::table('orangtuas', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn('siswa_id');
        });

        // Then add orangtua_id to siswas table
        Schema::table('siswas', function (Blueprint $table) {
            $table->foreignId('orangtua_id')->nullable()->after('academic_year_id')->constrained('orangtuas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Remove orangtua_id from siswas
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['orangtua_id']);
            $table->dropColumn('orangtua_id');
        });

        // Restore the original siswa_id in orangtuas
        Schema::table('orangtuas', function (Blueprint $table) {
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
        });
    }
};
