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
        // First, update empty strings to NULL in nip field
        DB::statement("UPDATE gurus SET nip = NULL WHERE nip = ''");
        
        // Check if the unique constraint exists
        $indexExists = DB::select("SHOW INDEXES FROM gurus WHERE Key_name = 'gurus_nip_unique'");
        
        if (!empty($indexExists)) {
            // Drop the existing unique constraint if it exists
            Schema::table('gurus', function (Blueprint $table) {
                $table->dropUnique('gurus_nip_unique');
            });
        }
        
        // For MySQL, we'll create a regular unique index
        // This will still allow multiple NULL values
        Schema::table('gurus', function (Blueprint $table) {
            $table->unique('nip', 'gurus_nip_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the custom unique constraint
        Schema::table('gurus', function (Blueprint $table) {
            DB::statement("ALTER TABLE gurus DROP INDEX gurus_nip_unique");
            
            // Add back the original unique constraint
            $table->unique('nip', 'gurus_nip_unique');
        });
    }
};
