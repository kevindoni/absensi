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
        Schema::table('siswas', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['orangtua_id']);
            }
            
            $table->foreignId('orangtua_id')->nullable()->change();
            
            $table->foreign('orangtua_id')->references('id')->on('orangtuas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['orangtua_id']);
            }
            
            $table->foreignId('orangtua_id')->nullable(false)->change(); // Revert to non-nullable if that was the original state
            
            $table->foreign('orangtua_id')->references('id')->on('orangtuas');
        });
    }
};
