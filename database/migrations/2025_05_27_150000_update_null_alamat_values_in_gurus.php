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
        // Update any NULL values in alamat field to '-'
        DB::table('gurus')
            ->whereNull('alamat')
            ->update(['alamat' => '-']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot revert this as we don't know which records originally had NULL values
    }
};
