<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use App\Models\OrangTua;
use App\Models\Siswa;
use Exception;

class BaileysWhatsAppService
{
    private $apiUrl;
    private $timeout = 30;
    
    public function __construct()
    {
        $this->apiUrl = Setting::getSetting('whatsapp_gateway_url', 'http://localhost:3001');
    }

    /**
     * Get connection status from Baileys service
     */
    public function getConnectionStatus()
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->apiUrl . '/api/status');
            
            if ($response->successful()) {
                $data = $response->json();
                $isConnected = $data['isConnected'] ?? false;
                
                // Track connection time changes
                if ($isConnected) {
                    $this->updateConnectionTime();
                } else {
                    $this->clearConnectionTime();
                }
                
                return [
                    'success' => true,
                    'connected' => $isConnected,
                    'state' => $data['connectionState'] ?? 'disconnected'
                ];
            }
            
            // Service not responding
            $this->clearConnectionTime();
            return [
                'success' => false,
                'connected' => false,
                'state' => 'disconnected',
                'error' => 'Service not responding'
            ];
            
        } catch (Exception $e) {
            // Clear connection time on error
            $this->clearConnectionTime();
            return [
                'success' => false,
                'connected' => false,
                'state' => 'disconnected',
                'error' => $e->getMessage()
            ];
        }
        }

    /**
     * Get QR Code for authentication
     */
    public function getQRCode()
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->apiUrl . '/api/qr-code');
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'qrCode' => $data['qrCode'] ?? null,
                    'state' => $data['connectionState'] ?? 'disconnected'
                ];
            }
            
            return [
                'success' => false,
                'qrCode' => null,
                'error' => 'Failed to get QR code'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'qrCode' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send text message
     */
    public function sendMessage($phone, $message)
    {
        try {
            // Validate phone number
            $formattedPhone = $this->formatPhoneNumber($phone);
            if (!$formattedPhone) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format'
                ];
            }

            $response = Http::timeout($this->timeout)->post($this->apiUrl . '/api/send-message', [
                'phone' => $formattedPhone,
                'message' => $message
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Track message statistics
                $this->updateMessageStats(true);
                
                return [
                    'success' => true,
                    'messageId' => $data['messageId'] ?? null,
                    'data' => $data
                ];
            }

            $error = $response->json()['error'] ?? 'Unknown error';

            // Track message statistics with failure
            $this->updateMessageStats(false);

            return [
                'success' => false,
                'error' => $error
            ];

        } catch (Exception $e) {
            // Track message statistics with failure
            $this->updateMessageStats(false);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send media message
     */
    public function sendMedia($phone, $mediaUrl, $caption = '', $mediaType = 'image')
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            if (!$formattedPhone) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format'
                ];
            }

            $response = Http::timeout($this->timeout)->post($this->apiUrl . '/api/send-media', [
                'phone' => $formattedPhone,
                'mediaUrl' => $mediaUrl,
                'caption' => $caption,
                'mediaType' => $mediaType
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'messageId' => $data['messageId'] ?? null,
                    'data' => $data
                ];
            }

            $error = $response->json()['error'] ?? 'Unknown error';
            return [
                'success' => false,
                'error' => $error
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Disconnect WhatsApp session
     */
    public function disconnect()
    {
        try {
            $response = Http::timeout($this->timeout)->post($this->apiUrl . '/api/disconnect');
            
            if ($response->successful()) {                // Update setting status
                Setting::updateOrCreate(
                    ['key' => 'whatsapp_session_status'],
                    ['value' => 'disconnected']
                );
                
                return [
                    'success' => true,
                    'message' => 'Disconnected successfully'
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to disconnect'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if WhatsApp gateway is enabled
     */
    public function isEnabled()
    {
        return Setting::getSetting('whatsapp_gateway_enabled', 'false') === 'true';
    }

    /**
     * Check if WhatsApp notifications are enabled
     */
    public function isNotificationsEnabled()
    {
        return Setting::getSetting('whatsapp_notifications_enabled', 'false') === 'true';
    }

    /**
     * Check if WhatsApp is connected
     */
    public function isConnected()
    {
        $status = $this->getConnectionStatus();
        return $status['connected'] ?? false;
    }

    /**
     * Get gateway URL
     */
    public function getGatewayUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Update gateway URL
     */
    public function updateGatewayUrl($url)
    {
        try {
            $this->apiUrl = rtrim($url, '/');
            
            // Optionally save to database
            Setting::updateOrCreate(
                ['key' => 'whatsapp_gateway_url'],
                ['value' => $this->apiUrl]
            );
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get message template
     */
    public function getMessageTemplate($type, $variables = [])
    {
        $template = Setting::getSetting('whatsapp_message_template_' . $type, '');
        
        if (empty($template)) {
            return $this->getDefaultTemplate($type);
        }
        
        // Replace variables in template
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Get default message templates
     */
    private function getDefaultTemplate($type)
    {
        $templates = [
            'late' => 'Halo {nama}, Anda terlambat hadir pada {tanggal} pukul {waktu}. Mohon segera konfirmasi.',
            'absent' => 'Halo {nama}, Anda belum hadir pada {tanggal}. Mohon segera konfirmasi kehadiran.',
            'admin' => 'Alert Admin: {pesan} pada {tanggal} {waktu}.'
        ];
        
        return $templates[$type] ?? 'Pesan dari Sistem Absensi.';
    }    /**
     * Get admin phone numbers
     */
    public function getAdminNumbers()
    {
        $numbers = Setting::getSetting('whatsapp_admin_numbers', '');
        
        if (empty($numbers)) {
            return [];
        }
        
        // Since numbers are stored as comma-separated string, use explode instead of json_decode
        $numbersArray = array_filter(array_map('trim', explode(',', $numbers)));
        return $numbersArray;
    }

    /**
     * Get parent phone numbers from database
     */
    public function getParentNumbers()
    {
        $parents = OrangTua::whereNotNull('no_telp')
                          ->where('no_telp', '!=', '')
                          ->pluck('no_telp')
                          ->toArray();
        
        $validNumbers = [];
        foreach ($parents as $number) {
            $formatted = $this->formatPhoneNumber($number);
            if ($formatted) {
                $validNumbers[] = $formatted;
            }
        }
        
        return array_unique($validNumbers);
    }

    /**
     * Get all notification recipient numbers (admin + parents)
     */
    public function getAllNotificationNumbers()
    {
        $adminNumbers = $this->getAdminNumbers();
        $parentNumbers = $this->getParentNumbers();
        
        // Combine and remove duplicates
        $allNumbers = array_unique(array_merge($adminNumbers, $parentNumbers));
        
        return array_values($allNumbers);
    }

    /**
     * Test connection to WhatsApp service
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/health');
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Service is running',
                    'data' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Service health check failed'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Cannot connect to WhatsApp service: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get message templates
     */
    public function getMessageTemplates()
    {
        return [
            'clock_in' => Setting::getSetting('whatsapp_template_clock_in', '🟢 *{name}* telah absen masuk pada {time}\n📍 Lokasi: {location}'),
            'clock_out' => Setting::getSetting('whatsapp_template_clock_out', '🔴 *{name}* telah absen keluar pada {time}\n📍 Lokasi: {location}'),
            'late' => Setting::getSetting('whatsapp_template_late', '⚠️ *{name}* terlambat masuk pada {time}\n⏰ Keterlambatan: {late_duration}'),
            'absent' => Setting::getSetting('whatsapp_template_absent', '❌ *{name}* tidak hadir hari ini\n📅 Tanggal: {date}')
        ];
    }

    /**
     * Update message templates
     */
    public function updateMessageTemplates($templates)
    {
        try {
            foreach ($templates as $type => $template) {
                Setting::updateOrCreate(
                    ['key' => 'whatsapp_template_' . $type],
                    ['value' => $template]
                );
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update admin numbers
     */
    public function updateAdminNumbers($numbers)
    {
        try {
            Setting::updateOrCreate(
                ['key' => 'whatsapp_admin_numbers'],
                ['value' => implode(',', $numbers)]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Format phone number to Indonesian format
     */
    public function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return false;
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Handle Indonesian phone numbers
        if (strlen($phone) < 10) {
            return false;
        }
        
        // Convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } else if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
          // Validate Indonesian mobile number patterns
        // Include both 62 prefix (international) and specific operator codes
        $validPrefixes = ['62', '628', '6281', '6282', '6283', '6285', '6287', '6288', '6289'];
        $isValid = false;
        
        foreach ($validPrefixes as $prefix) {
            if (substr($phone, 0, strlen($prefix)) === $prefix) {
                $isValid = true;
                break;
            }
        }
        
        if (!$isValid || strlen($phone) < 12 || strlen($phone) > 15) {
            return false;
        }
        
        return $phone;
    }
    
    /**
     * Get attendance notification templates
     */
    public function getAttendanceTemplates()
    {
        return [
            'check_in' => Setting::getSetting('whatsapp_template_check_in', '🟢 *{name}* telah absen masuk pada {time}\n📍 Lokasi: {location}'),
            'check_out' => Setting::getSetting('whatsapp_template_check_out', '🔴 *{name}* telah absen keluar pada {time}\n📍 Lokasi: {location}'),
            'late' => Setting::getSetting('whatsapp_template_late', '⚠️ *{name}* terlambat masuk pada {time}\n⏰ Keterlambatan: {late_duration}'),
            'absent' => Setting::getSetting('whatsapp_template_absent', '❌ *{name}* tidak hadir hari ini\n📅 Tanggal: {date}'),
            'sick' => Setting::getSetting('whatsapp_template_sick', '🏥 *{name}* tidak hadir karena sakit\n📅 Tanggal: {date}'),
            'permission' => Setting::getSetting('whatsapp_template_permission', '📝 *{name}* tidak hadir karena izin\n📅 Tanggal: {date}')
        ];
    }    /**
     * Send attendance notification to specific parent
     */
    public function sendAttendanceNotification($siswaId, $templateType, $variables = [])
    {
        try {
            // Get student data
            $siswa = Siswa::with('orangtua', 'kelas')->find($siswaId);
            
            if (!$siswa) {
                return false;
            }

            // Get parent phone number
            if (!$siswa->orangtua || !$siswa->orangtua->no_telp) {
                return false;
            }

            $parentPhone = $this->formatPhoneNumber($siswa->orangtua->no_telp);
            if (!$parentPhone) {
                return false;
            }

            // Get template
            $templates = $this->getAttendanceTemplates();
            $template = $templates[$templateType] ?? null;

            if (!$template) {
                return false;
            }

            // Prepare template variables matching the admin interface variables
            $templateVariables = array_merge([
                'school_name' => Setting::getSetting('school_name', 'Sekolah'),
                'nama_siswa' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas ? $siswa->kelas->nama_kelas : 'N/A',
                'tanggal' => now()->format('d/m/Y'),
                'waktu' => now()->format('H:i'),
                'status' => $this->getStatusFromTemplateType($templateType),
                'keterangan' => 'Notifikasi test dari sistem'
            ], $variables);

            // Replace variables in template
            $message = $this->replaceTemplateVariables($template, $templateVariables);

            // Send message
            $result = $this->sendMessage($parentPhone, $message);

            if ($result['success']) {
                return true;
            } else {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get status text from template type
     */
    private function getStatusFromTemplateType($templateType)
    {
        $statusMapping = [
            'check_in' => 'Hadir',
            'check_out' => 'Pulang',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'sick' => 'Sakit',
            'permission' => 'Izin'
        ];
        
        return $statusMapping[$templateType] ?? 'Unknown';
    }

    /**
     * Send bulk attendance notifications
     */
    public function sendBulkAttendanceNotifications($attendanceData)
    {
        $results = [
            'total' => count($attendanceData),
            'sent' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($attendanceData as $data) {
            $success = $this->sendAttendanceNotification(
                $data['siswa_id'],
                $data['template_type'],
                $data['variables'] ?? []
            );

            if ($success) {
                $results['sent']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to send notification for student ID: {$data['siswa_id']}";
            }
        }

        return $results;
    }

    /**
     * Replace template variables
     */
    private function replaceTemplateVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }    /**
     * Get default attendance template variables
     */
    public function getTemplateVariables()
    {
        return [
            'school_name' => 'Nama sekolah',
            'nama_siswa' => 'Nama siswa',
            'kelas' => 'Nama kelas',
            'tanggal' => 'Tanggal (DD/MM/YYYY)',
            'waktu' => 'Waktu (HH:MM)',
            'status' => 'Status kehadiran',
            'keterangan' => 'Keterangan tambahan'
        ];
    }    /**
     * Check gateway status and availability
     */
    public function checkGatewayStatus()
    {
        try {
            $response = Http::timeout(10)->get($this->apiUrl . '/health');
            
            if ($response->successful()) {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Track message statistics
     */
    private function updateMessageStats($success = true)
    {
        try {
            $totalSent = (int)Setting::getSetting('whatsapp_total_sent', 0);
            Setting::setSetting('whatsapp_total_sent', $totalSent + 1);
            
            $successCount = (int)Setting::getSetting('whatsapp_success_count', 0);
            if ($success) {
                Setting::setSetting('whatsapp_success_count', $successCount + 1);
            }
            
            if ($totalSent > 0) {
                $successRate = round(($successCount / $totalSent) * 100, 1);
                Setting::setSetting('whatsapp_success_rate', $successRate);
            }
        } catch (\Exception $e) {
            // Log error but don't throw
            \Log::error('Failed to update message stats: ' . $e->getMessage());
        }
    }

    /**
     * Track connection time
     */
    private function updateConnectionTime()
    {
        try {
            $currentTime = Setting::getSetting('whatsapp_connection_time');
            
            // Only set if not already set (preserve original connection time)
            if (!$currentTime) {
                Setting::updateOrCreate(
                    ['key' => 'whatsapp_connection_time'],
                    ['value' => time()]
                );
            }
        } catch (\Exception $e) {
            // Log error but don't throw
            \Log::error('Failed to update connection time: ' . $e->getMessage());
        }
    }

    /**
     * Clear connection time
     */
    private function clearConnectionTime()
    {
        try {
            Setting::updateOrCreate(
                ['key' => 'whatsapp_connection_time'],
                ['value' => null]
            );
        } catch (\Exception $e) {
            // Log error but don't throw
            \Log::error('Failed to clear connection time: ' . $e->getMessage());
        }
    }
}
