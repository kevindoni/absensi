<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\BaileysWhatsAppService;
use App\Services\AdminNotificationService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * Send test message
     */
    public function sendTestMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phoneNumber = $this->whatsappService->formatPhoneNumber($request->phone_number);
            
            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format nomor telepon tidak valid'
                ], 422);
            }

            $result = $this->whatsappService->sendMessage($phoneNumber, $request->message);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan test berhasil dikirim ke ' . $phoneNumber
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan test'
            ], 400);

        } catch (\Exception $e) {
            
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
     * Send test attendance notification
     */
    public function sendTestAttendanceNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'siswa_id' => 'required|exists:siswas,id',
                'template_type' => 'required|in:check_in,check_out,late,absent,sick,permission',
                'time' => 'nullable|string',
                'date' => 'nullable|string',
                'location' => 'nullable|string',
                'late_duration' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare variables for template
            $variables = [
                'time' => $request->time ?: now()->format('H:i'),
                'date' => $request->date ?: now()->format('d/m/Y'),
                'location' => $request->location ?: 'Sekolah',
                'late_duration' => $request->late_duration ?: '15 menit'
            ];

            // Send test notification
            $result = $this->whatsappService->sendAttendanceNotification(
                $request->siswa_id,
                $request->template_type,
                $variables
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notifikasi kehadiran berhasil dikirim'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim test notifikasi kehadiran'
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
    public function systemHealth()
    {
        try {
            $data = [
                'whatsapp_connected' => $this->whatsappService->isConnected(),
                'gateway_status' => $this->whatsappService->checkGatewayStatus(),
                'database_connected' => $this->checkDatabaseConnection(),
                'admin_count' => count($this->whatsappService->getAdminNumbers()),
                'parent_count' => count($this->whatsappService->getParentNumbers())
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'System health check completed'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pengecekan kesehatan system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset templates to default values
     */
    public function resetTemplatesToDefault()
    {
        try {
            // Default attendance templates
            $defaultTemplates = [
                'whatsapp_template_check_in' => '🟢 Notifikasi Kehadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
✅ *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas perhatiannya.',

                'whatsapp_template_late' => '⚠️ Notifikasi Keterlambatan dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
⏰ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon perhatian untuk kedisiplinan anak.',

                'whatsapp_template_absent' => '❌ Notifikasi Ketidakhadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
❌ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon konfirmasi mengenai ketidakhadiran anak.',

                'whatsapp_template_sick' => '🏥 Notifikasi Sakit dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🏥 *Status*: {status}
📝 *Keterangan*: {keterangan}

Semoga lekas sembuh.',

                'whatsapp_template_permission' => '📄 Notifikasi Izin dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
📄 *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas pemberitahuannya.',

                'whatsapp_template_check_out' => '🔴 Notifikasi Pulang dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
🔴 *Status*: {status}
📝 *Keterangan*: {keterangan}

Anak telah pulang dengan selamat.'
            ];

            // Reset all templates to default
            foreach ($defaultTemplates as $key => $value) {
                Setting::setSetting($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil direset ke pengaturan default'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
