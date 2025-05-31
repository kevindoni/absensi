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
        Schema::table('kelas', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['academic_year_id']);
            
            // Make the column nullable
            $table->foreignId('academic_year_id')->nullable()->change();
            
            // Add the foreign key back with nullOnDelete
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['academic_year_id']);
            
            // Make the column required again
            $table->foreignId('academic_year_id')->nullable(false)->change();
            
            // Add the foreign key back with cascade delete
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
        });
    }
};
