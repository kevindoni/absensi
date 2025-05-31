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
        // Optimize sessions table for better performance
        Schema::table('sessions', function (Blueprint $table) {
            // Add index on last_activity for faster cleanup
            if (!Schema::hasIndex('sessions', 'sessions_last_activity_index')) {
                $table->index('last_activity');
            }
            
            // Add index on user_id for faster user session lookup
            if (!Schema::hasIndex('sessions', 'sessions_user_id_index')) {
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['last_activity']);
            $table->dropIndex(['user_id']);
        });
    }
};
