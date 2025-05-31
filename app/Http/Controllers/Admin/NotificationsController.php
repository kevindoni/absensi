<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{    /**
     * Show notifications settings page
     */
    public function settings()
    {
        $settings = [
            'email_notifications' => $this->getBoolSetting('email_notifications', true),
            'notify_parent_on_absence' => $this->getBoolSetting('notify_parent_on_absence', true),
            'notification_email_template' => Setting::getSetting('notification_email_template', 
                'Yth. {nama_ortu}, kami informasikan bahwa {nama_siswa} tidak hadir di sekolah pada tanggal {tanggal} dengan status {status}.'),
        ];
        
        return view('admin.notifications.settings', compact('settings'));
    }
      /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $validatedData = $request->validate([
            'email_notifications' => 'nullable|in:1',
            'notify_parent_on_absence' => 'nullable|in:1',
            'notification_email_template' => 'nullable|string',
        ]);
        
        // Update email notifications setting
        $this->updateSetting('email_notifications', isset($validatedData['email_notifications']) ? 'true' : 'false');
        
        // Update parent notification setting
        $this->updateSetting('notify_parent_on_absence', isset($validatedData['notify_parent_on_absence']) ? 'true' : 'false');
        
        // Update templates if provided
        if (isset($validatedData['notification_email_template'])) {
            $this->updateSetting('notification_email_template', $validatedData['notification_email_template']);
        }
        
        return redirect()->route('admin.notifications.settings')
            ->with('success', 'Pengaturan notifikasi berhasil diperbarui.');
    }
    
    /**
     * Get boolean setting
     */
    private function getBoolSetting($key, $default = false)
    {
        $value = Setting::getSetting($key, $default ? 'true' : 'false');
        return $value === 'true';
    }
      /**
     * Update setting
     */
    private function updateSetting($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            Setting::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }
      /**
     * Test notification sending
     */      public function testNotification()
    {
        // Run the test notification command with force flag and capture output
        $exitCode = \Artisan::call('notification:test', ['--force' => true], $output = new \Symfony\Component\Console\Output\BufferedOutput);
        $output = \Artisan::output();
        
        // Also send a system notification to the admin
        $adminNotificationService = new \App\Services\AdminNotificationService();
        $adminNotificationService->sendTestNotification();
        
        // Get notification settings
        $settings = [
            'email_notifications' => $this->getBoolSetting('email_notifications', true),
            'notify_parent_on_absence' => $this->getBoolSetting('notify_parent_on_absence', true),
        ];
        
        return view('admin.notifications.test', [
            'output' => $output,
            'settings' => $settings,
            'exitCode' => $exitCode
        ]);
    }
}
