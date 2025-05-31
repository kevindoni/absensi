<?php

namespace App\Services;

use App\Models\Admin;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;

class AdminNotificationService
{
    /**
     * Send notification to all admin users
     *
     * @param string $message The notification message
     * @param string $title Optional notification title
     * @param string $type Notification type (info, success, warning, danger)
     * @param string|null $url Optional URL to link the notification to
     * @param string $icon Optional FontAwesome icon name
     * @return void
     */
    public function sendAdminNotification($message, $title = null, $type = 'info', $url = null, $icon = null)
    {
        try {
            $admins = Admin::all();
            
            if ($admins->isEmpty()) {
                Log::warning('No admin users found to send notification to');
                return false;
            }
            
            // Set default icon based on type if not provided
            if (!$icon) {
                $icon = $this->getIconForType($type);
            }
            
            $notificationData = [
                'title' => $title ?: 'Notifikasi Sistem',
                'message' => $message,
                'type' => $type,
                'icon' => $icon,
                'url' => $url
            ];
            
            foreach ($admins as $admin) {
                $admin->notify(new SystemNotification($notificationData));
            }
            
            Log::info('Admin notification sent successfully', [
                'message' => $message,
                'admin_count' => $admins->count()
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending admin notification', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Send test notification to all admins
     *
     * @return bool
     */
    public function sendTestNotification()
    {
        return $this->sendAdminNotification(
            'Ini adalah notifikasi test. Sistem notifikasi berfungsi dengan baik!',
            'Test Notifikasi',
            'success',
            null,
            'check-circle'
        );
    }
    
    /**
     * Get appropriate icon for notification type
     *
     * @param string $type
     * @return string
     */
    private function getIconForType($type)
    {
        switch ($type) {
            case 'success':
                return 'check-circle';
            case 'warning':
                return 'exclamation-triangle';
            case 'danger':
                return 'times-circle';
            case 'info':
            default:
                return 'bell';
        }
    }
}
