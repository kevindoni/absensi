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
        // Remove M-Pedia settings jika ada
        Setting::whereIn('key', [
            'mpedia_api_url',
            'mpedia_device_route',
            'mpedia_send_route',
            'mpedia_api_token',
            'mpedia_device_phone',
            'mpedia_use_md'
        ])->delete();        // Add new WhatsApp Baileys Gateway settings
        $settings = [
            'whatsapp_gateway_enabled' => 'false',
            'whatsapp_gateway_url' => 'http://localhost:3001',
            'whatsapp_session_status' => 'disconnected',
            'whatsapp_last_connected' => '',
            'whatsapp_notifications_enabled' => 'false',
            'whatsapp_admin_numbers' => '', // JSON array of admin phone numbers
            'whatsapp_message_template_clock_in' => '🟢 *{name}* telah absen masuk pada {time}\\n📍 Lokasi: {location}',
            'whatsapp_message_template_clock_out' => '🔴 *{name}* telah absen keluar pada {time}\\n📍 Lokasi: {location}',
            'whatsapp_message_template_late' => '⚠️ *{name}* terlambat masuk pada {time}\\n⏰ Keterlambatan: {late_duration}',
            'whatsapp_message_template_absent' => '❌ *{name}* tidak hadir hari ini\\n📅 Tanggal: {date}'
        ];        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove WhatsApp Baileys settings
        Setting::whereIn('key', [
            'whatsapp_gateway_enabled',
            'whatsapp_gateway_url',
            'whatsapp_session_status',
            'whatsapp_last_connected',
            'whatsapp_notifications_enabled',
            'whatsapp_admin_numbers',
            'whatsapp_message_template_late',
            'whatsapp_message_template_absent',
            'whatsapp_message_template_admin'
        ])->delete();

        // Restore M-Pedia settings if needed
        $mpedia_settings = [
            'mpedia_api_url' => 'https://digindo.my.id',
            'mpedia_device_route' => '/device',
            'mpedia_send_route' => '/send_message.php',
            'mpedia_api_token' => '',
            'mpedia_device_phone' => '',
            'mpedia_use_md' => 'true'
        ];

        foreach ($mpedia_settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }
};
