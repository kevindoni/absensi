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
        // First ensure we have at least one row in settings table
        $settingsCount = DB::table('settings')->count();
        if ($settingsCount === 0) {
            DB::table('settings')->insert([
                'key' => 'app_settings',
                'value' => '{}',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Add the new settings with default values
        Setting::updateOrCreate(
            ['key' => 'late_tolerance_minutes'],
            ['value' => '5'] // 5 minutes tolerance by default
        );

        Setting::updateOrCreate(
            ['key' => 'max_late_minutes'],
            ['value' => '30'] // 30 minutes maximum by default
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'late_tolerance_minutes')->delete();
        Setting::where('key', 'max_late_minutes')->delete();
    }
};
