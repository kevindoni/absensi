<?php

namespace App\Services;

use App\Models\Admin;
use App\Notifications\SystemNotification;
use App\Services\WhatsApp\BaileysWhatsAppService;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class AdminNotificationService
{
    protected $whatsappService;

    public function __construct(BaileysWhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }    /**
     * Send notification to all admin users
     *
     * @param string $message The notification message
     * @param string $title Optional notification title
     * @param string $type Notification type (info, success, warning, danger)
     * @param string|null $url Optional URL to link the notification to
     * @param string $icon Optional FontAwesome icon name
     * @param bool $sendWhatsApp Whether to send WhatsApp notification
     * @return void
     */
    public function sendAdminNotification($message, $title = null, $type = 'info', $url = null, $icon = null, $sendWhatsApp = true)
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

            // Send WhatsApp notification if enabled
            if ($sendWhatsApp) {
                $this->sendWhatsAppNotification($message, $title);
            }
            
            Log::info('Admin notification sent successfully', [
                'message' => $message,
                'admin_count' => $admins->count(),
                'whatsapp_sent' => $sendWhatsApp
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending admin notification', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }    /**
     * Send WhatsApp notification to admin and parent numbers
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    private function sendWhatsAppNotification($message, $title = null)
    {
        try {
            if (!$this->whatsappService->isConnected()) {
                Log::warning('WhatsApp service is not connected, skipping WhatsApp notification');
                return;
            }

            $allNumbers = $this->whatsappService->getAllNotificationNumbers();
            
            if (empty($allNumbers)) {
                Log::info('No WhatsApp numbers configured for notifications');
                return;
            }

            $fullMessage = $title ? "*{$title}*\n\n{$message}" : $message;

            foreach ($allNumbers as $number) {
                dispatch(new SendWhatsAppMessage($number, $fullMessage));
            }

            Log::info('WhatsApp notifications queued for all recipients', [
                'total_count' => count($allNumbers),
                'admin_count' => count($this->whatsappService->getAdminNumbers()),
                'parent_count' => count($this->whatsappService->getParentNumbers())
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp notification to admins', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send attendance alert to admins
     *
     * @param string $employeeName
     * @param string $action (clock_in, clock_out, late, absent)
     * @param string $time
     * @param array $additionalData
     * @return bool
     */
    public function sendAttendanceAlert($employeeName, $action, $time, $additionalData = [])
    {
        $messages = [
            'clock_in' => "🟢 Karyawan *{$employeeName}* telah absen masuk pada {$time}",
            'clock_out' => "🔴 Karyawan *{$employeeName}* telah absen keluar pada {$time}",
            'late' => "⚠️ Karyawan *{$employeeName}* terlambat masuk pada {$time}",
            'absent' => "❌ Karyawan *{$employeeName}* tidak hadir hari ini"
        ];

        $message = $messages[$action] ?? "📋 Update absensi untuk *{$employeeName}* pada {$time}";
        
        // Add additional information
        if (!empty($additionalData['location'])) {
            $message .= "\n📍 Lokasi: " . $additionalData['location'];
        }
        
        if (!empty($additionalData['photo'])) {
            $message .= "\n📸 Foto tersedia";
        }

        return $this->sendAdminNotification(
            $message,
            'Alert Absensi',
            $action === 'late' ? 'warning' : ($action === 'absent' ? 'danger' : 'info'),
            null,
            null,
            true // Send WhatsApp
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
            'check-circle',
            false // Don't send WhatsApp for test
        );
    }

    /**
     * Send test WhatsApp notification
     *
     * @return bool
     */
    public function sendTestWhatsAppNotification()
    {
        return $this->sendAdminNotification(
            'Ini adalah test notifikasi WhatsApp. Sistem WhatsApp berfungsi dengan baik! 🚀',
            'Test WhatsApp',
            'success',
            null,
            'check-circle',
            true // Send WhatsApp only
        );
    }
}
