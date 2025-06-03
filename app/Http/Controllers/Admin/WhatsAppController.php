<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\BaileysWhatsAppService;
use App\Services\AdminNotificationService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    protected $whatsappService;
    protected $notificationService;

    public function __construct(BaileysWhatsAppService $whatsappService, AdminNotificationService $notificationService)
    {
        $this->whatsappService = $whatsappService;
        $this->notificationService = $notificationService;
    }    /**
     * Display WhatsApp settings page
     */
    public function index()
    {
        $data = [
            'title' => 'Pengaturan WhatsApp',
            'isConnected' => $this->whatsappService->isConnected(),
            'gatewayUrl' => $this->whatsappService->getGatewayUrl(),
            'adminNumbers' => $this->whatsappService->getAdminNumbers(),
            'parentNumbers' => $this->whatsappService->getParentNumbers(),
            'allNumbers' => $this->whatsappService->getAllNotificationNumbers(),
            'messageTemplates' => $this->whatsappService->getMessageTemplates(),
            'attendanceTemplates' => $this->whatsappService->getAttendanceTemplates(),
            'templateVariables' => $this->whatsappService->getTemplateVariables()
        ];

        return view('admin.whatsapp.index', $data);
    }

    /**
     * Get QR Code for WhatsApp connection
     */
    public function getQRCode()
    {
        try {
            $serviceResponse = $this->whatsappService->getQRCode(); // Renamed variable for clarity
            
            if ($serviceResponse && isset($serviceResponse['success']) && $serviceResponse['success'] && isset($serviceResponse['qrCode'])) {
                return response()->json([
                    'success' => true,
                    'qr_code' => $serviceResponse['qrCode'], // Directly use the qrCode from the service
                    'message' => 'QR Code berhasil diambil'
                ]);
            }

            // Handle cases where qrCode is not set or serviceResponse indicates failure
            $errorMessage = 'Gagal mengambil QR Code.';
            if ($serviceResponse && isset($serviceResponse['error'])) {
                $errorMessage = $serviceResponse['error'];
            } else if ($serviceResponse && isset($serviceResponse['message'])) {
                $errorMessage = $serviceResponse['message'];
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 400);        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get connection status
     */
    public function getStatus()
    {
        try {
            $status = $this->whatsappService->getConnectionStatus();
            
            return response()->json([
                'success' => true,
                'status' => $status,
                'connected' => $this->whatsappService->isConnected()
            ]);        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status koneksi'
            ], 500);
        }
    }

    /**
     * Disconnect WhatsApp
     */
    public function disconnect()
    {
        try {
            $result = $this->whatsappService->disconnect();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp berhasil disconnected'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal disconnect WhatsApp'
            ], 400);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update gateway settings
     */
    public function updateGateway(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'URL gateway tidak valid'
            ], 422);
        }

        try {
            $result = $this->whatsappService->updateGatewayUrl($request->gateway_url);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gateway URL berhasil diupdate'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate gateway URL'
            ], 400);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin numbers
     */
    public function updateAdminNumbers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_numbers' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor admin harus diisi'
            ], 422);
        }

        try {
            // Parse and validate phone numbers
            $numbers = array_filter(array_map('trim', explode(',', $request->admin_numbers)));
            $validNumbers = [];

            foreach ($numbers as $number) {
                $formatted = $this->whatsappService->formatPhoneNumber($number);
                if ($formatted) {
                    $validNumbers[] = $formatted;
                }
            }

            if (empty($validNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada nomor yang valid'
                ], 422);
            }

            $result = $this->whatsappService->updateAdminNumbers($validNumbers);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nomor admin berhasil diupdate',
                    'valid_numbers' => $validNumbers
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate nomor admin'
            ], 400);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update message templates
     */
    public function updateTemplates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clock_in_template' => 'required|string|max:500',
            'clock_out_template' => 'required|string|max:500',
            'late_template' => 'required|string|max:500',
            'absent_template' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Template pesan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $templates = [
                'clock_in' => $request->clock_in_template,
                'clock_out' => $request->clock_out_template,
                'late' => $request->late_template,
                'absent' => $request->absent_template
            ];

            $result = $this->whatsappService->updateMessageTemplates($templates);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template pesan berhasil diupdate'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate template pesan'
            ], 400);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update attendance templates
     */
    public function updateAttendanceTemplates(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'check_in' => 'required|string|max:500',
                'check_out' => 'required|string|max:500',
                'late' => 'required|string|max:500',
                'absent' => 'required|string|max:500',
                'sick' => 'required|string|max:500',
                'permission' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update templates in settings
            Setting::setSetting('whatsapp_template_check_in', $request->check_in);
            Setting::setSetting('whatsapp_template_check_out', $request->check_out);
            Setting::setSetting('whatsapp_template_late', $request->late);
            Setting::setSetting('whatsapp_template_absent', $request->absent);
            Setting::setSetting('whatsapp_template_sick', $request->sick);
            Setting::setSetting('whatsapp_template_permission', $request->permission);

            return response()->json([
                'success' => true,
                'message' => 'Template notifikasi kehadiran berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test message with rate limiting
     */
    public function sendTestMessage(Request $request)
    {
        // Rate limiting: max 5 test messages per minute per IP
        $key = 'test_message_' . $request->ip();
        $attempts = cache($key, 0);
        
        if ($attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please wait before sending more test messages.'
            ], 429);
        }
        
        cache([$key => $attempts + 1], now()->addMinute());

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^[0-9+\-\s\(\)]+$/',
            'message' => 'required|string|max:1000|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Enhanced phone number validation
            $phoneNumber = $this->whatsappService->formatPhoneNumber($request->phone_number);
            
            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxx atau +628xxxxxxxxx'
                ], 422);
            }

            // Check if WhatsApp is connected before attempting to send
            if (!$this->whatsappService->isConnected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp tidak terhubung. Silakan hubungkan terlebih dahulu.'
                ], 503);
            }

            $result = $this->whatsappService->sendMessage($phoneNumber, $request->message);
            
            if ($result) {
                // Log successful test message
                \Log::info('Test message sent successfully', [
                    'phone' => $phoneNumber,
                    'message_length' => strlen($request->message),
                    'user_ip' => $request->ip()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan test berhasil dikirim ke ' . $phoneNumber
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan test. Silakan periksa koneksi WhatsApp.'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Test message failed', [
                'error' => $e->getMessage(),
                'phone' => $request->phone_number,
                'user_ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test notification to admins
     */
    public function sendTestNotification()
    {
        try {
            $result = $this->notificationService->sendTestWhatsAppNotification();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notifikasi berhasil dikirim ke admin'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim test notifikasi'
            ], 400);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system health status
     */
    public function getSystemHealth()
    {
        try {
            $isConnected = $this->whatsappService->isConnected();
            $healthData = [
                'db_connected' => $this->checkDatabaseConnection(),
                'whatsapp_connected' => $isConnected,
                'admin_count' => count($this->whatsappService->getAdminNumbers()),
                'parent_count' => count($this->whatsappService->getParentNumbers()),
                'success_rate' => Setting::getSetting('whatsapp_success_rate', 0),
                'uptime' => $isConnected ? $this->getReadableUptime() : '0h 0m'
            ];

            return response()->json([
                'success' => true,
                'data' => $healthData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system health status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get WhatsApp service stats
     */
    public function getStats()
    {
        try {
            $isConnected = $this->whatsappService->isConnected();
            
            $healthData = [
                'total_sent' => Setting::getSetting('whatsapp_total_sent', 0),
                'success_rate' => Setting::getSetting('whatsapp_success_rate', 0),
                'uptime' => $isConnected ? $this->getReadableUptime() : '0h 0m'
            ];

            return response()->json([
                'success' => true,
                'data' => $healthData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get WhatsApp stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check database connection status
     */
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get readable uptime duration
     */
    private function getReadableUptime()
    {
        try {
            // First try to get uptime from WhatsApp service health endpoint
            $serviceUptime = $this->getServiceUptime();
            if ($serviceUptime !== null) {
                return $this->formatUptime($serviceUptime);
            }
            
            // Fallback to connection time tracking
            $startTime = Setting::getSetting('whatsapp_connection_time');
            if (!$startTime) {
                return '0h 0m';
            }
            
            $start = \Carbon\Carbon::createFromTimestamp($startTime);
            $now = \Carbon\Carbon::now();
            
            $hours = $now->diffInHours($start);
            $minutes = $now->diffInMinutes($start) % 60;
            
            return "{$hours}h {$minutes}m";
        } catch (\Exception $e) {
            return '0h 0m';
        }
    }
    
    /**
     * Get uptime from WhatsApp service health endpoint
     */
    private function getServiceUptime()
    {
        try {
            $gatewayUrl = $this->whatsappService->getGatewayUrl();
            $response = Http::timeout(5)->get($gatewayUrl . '/health');
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['uptime'] ?? null; // uptime in seconds
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Format uptime seconds into readable format
     */
    private function formatUptime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($hours > 24) {
            $days = floor($hours / 24);
            $hours = $hours % 24;
            return "{$days}d {$hours}h {$minutes}m";
        }
        
        return "{$hours}h {$minutes}m";
    }
}
