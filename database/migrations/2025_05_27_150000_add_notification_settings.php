<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {        // Add notification settings with default values
        $settings = [
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'notify_parent_on_absence',
                'value' => 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'notification_email_template',
                'value' => 'Yth. {nama_ortu}, kami informasikan bahwa {nama_siswa} tidak hadir di sekolah pada tanggal {tanggal} dengan status {status}.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert settings if they don't exist
        foreach ($settings as $setting) {
            if (!DB::table('settings')->where('key', $setting['key'])->exists()) {
                DB::table('settings')->insert($setting);
            }
        }
    }    public function down(): void
    {
        // Remove notification settings
        DB::table('settings')->whereIn('key', [
            'email_notifications',
            'notify_parent_on_absence',
            'notification_email_template',
        ])->delete();
    }
};
