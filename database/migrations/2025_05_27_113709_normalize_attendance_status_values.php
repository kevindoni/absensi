<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize 'Hadir' to 'hadir'
        DB::table('absensis')->where('status', 'Hadir')->update(['status' => 'hadir']);
        
        // Normalize 'Izin' to 'izin'
        DB::table('absensis')->where('status', 'Izin')->update(['status' => 'izin']);
        
        // Normalize 'Sakit' to 'sakit'
        DB::table('absensis')->where('status', 'Sakit')->update(['status' => 'sakit']);
        
        // Normalize 'Alpha' and 'Alpa' to 'alpa'
        DB::table('absensis')->whereIn('status', ['Alpha', 'Alpa'])->update(['status' => 'alpa']);
        
        // Additional check to handle any other variation case
        DB::statement("UPDATE absensis SET status = LOWER(status) WHERE status IN ('HADIR', 'IZIN', 'SAKIT', 'ALPA', 'ALPHA')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need for down migration as we're only standardizing case
    }
};
