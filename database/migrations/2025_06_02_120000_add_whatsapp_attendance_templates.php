<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add WhatsApp attendance template settings with default values
        $templates = [
            [
                'key' => 'whatsapp_template_check_in',
                'value' => '🟢 Notifikasi Kehadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
✅ *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas perhatiannya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_template_late',
                'value' => '⚠️ Notifikasi Keterlambatan dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
⏰ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon perhatian untuk kedisiplinan anak.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_template_absent',
                'value' => '❌ Notifikasi Ketidakhadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
❌ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon konfirmasi mengenai ketidakhadiran anak.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_template_sick',
                'value' => '🏥 Notifikasi Sakit dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🏥 *Status*: {status}
📝 *Keterangan*: {keterangan}

Semoga lekas sembuh.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_template_permission',
                'value' => '📄 Notifikasi Izin dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
📄 *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas pemberitahuannya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_template_check_out',
                'value' => '🔴 Notifikasi Pulang dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
🔴 *Status*: {status}
📝 *Keterangan*: {keterangan}

Anak telah pulang dengan selamat.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert templates if they don't exist
        foreach ($templates as $template) {
            if (!DB::table('settings')->where('key', $template['key'])->exists()) {
                DB::table('settings')->insert($template);
            }
        }
        
        // Also add general WhatsApp settings if they don't exist
        $generalSettings = [
            [
                'key' => 'whatsapp_gateway_enabled',
                'value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_notifications_enabled',
                'value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'enable_whatsapp_attendance_notifications',
                'value' => 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_gateway_url',
                'value' => 'http://localhost:3001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_admin_numbers',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($generalSettings as $setting) {
            if (!DB::table('settings')->where('key', $setting['key'])->exists()) {
                DB::table('settings')->insert($setting);
            }
        }
    }

    public function down(): void
    {
        // Remove WhatsApp attendance template settings
        DB::table('settings')->whereIn('key', [
            'whatsapp_template_check_in',
            'whatsapp_template_late',
            'whatsapp_template_absent',
            'whatsapp_template_sick',
            'whatsapp_template_permission',
            'whatsapp_template_check_out',
            'whatsapp_gateway_enabled',
            'whatsapp_notifications_enabled',
            'enable_whatsapp_attendance_notifications',
            'whatsapp_gateway_url',
            'whatsapp_admin_numbers',
        ])->delete();
    }
};
