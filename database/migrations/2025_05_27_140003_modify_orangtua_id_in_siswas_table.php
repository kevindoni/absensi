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
        Schema::table('siswas', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['orangtua_id']);
            
            // Make the column nullable
            $table->foreignId('orangtua_id')->nullable()->change();
            
            // Add the foreign key back with nullOnDelete
            $table->foreign('orangtua_id')->references('id')->on('orangtuas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['orangtua_id']);
            
            // Make the column required again
            $table->foreignId('orangtua_id')->nullable(false)->change();
            
            // Add the foreign key back without nullOnDelete
            $table->foreign('orangtua_id')->references('id')->on('orangtuas');
        });
    }
};
