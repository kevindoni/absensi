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
        // Add the new setting with default value (enabled by default)
        Setting::updateOrCreate(
            ['key' => 'enable_late_tolerance_system'],
            ['value' => '1'] // Enabled by default
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'enable_late_tolerance_system')->delete();
    }
};
