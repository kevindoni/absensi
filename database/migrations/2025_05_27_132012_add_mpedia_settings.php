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
        Schema::table('settings', function (Blueprint $table) {
            //
        });

        // Update default settings for MPWA MD v8.0.0
        $settings = [
            'mpedia_api_url' => config('mpedia.default_api_url', 'https://digindo.my.id'),
            'mpedia_device_route' => config('mpedia.default_device_route', '/device'),
            'mpedia_send_route' => config('mpedia.default_send_route', '/send_message.php'),
            'mpedia_api_token' => '',
            'mpedia_device_phone' => '', // phone number that will be used to send messages
            'mpedia_use_md' => 'true'    // enable multi-device support
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });

        // Remove M-Pedia settings
        Setting::whereIn('key', [
            'mpedia_api_url',
            'mpedia_api_token',
            'mpedia_device_phone',
            'mpedia_use_md'
        ])->delete();
    }
};
