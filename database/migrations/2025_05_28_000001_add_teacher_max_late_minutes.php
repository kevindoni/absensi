<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new settings with default values for teacher
        Setting::updateOrCreate(
            ['key' => 'teacher_max_late_minutes'],
            ['value' => '5'] // 5 minutes maximum for teachers
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'teacher_max_late_minutes')->delete();
    }
};
