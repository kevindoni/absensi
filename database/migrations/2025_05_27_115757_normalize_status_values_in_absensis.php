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
        // Update capitalized status values to lowercase
        DB::statement("UPDATE absensis SET status = LOWER(status) WHERE status <> LOWER(status)");
        
        // Normalize 'alpha' to 'alpa' (in case both spellings exist)
        DB::statement("UPDATE absensis SET status = 'alpa' WHERE status = 'alpha'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert this operation
    
    }
};
