<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Setting;
use App\Services\WhatsApp\BaileysWhatsAppService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceNotificationService
{
    private $whatsappService;

    public function __construct(BaileysWhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Send attendance notification to parent
     */
    public function sendAttendanceNotification(Absensi $absensi)
    {
        try {
            // Check if WhatsApp attendance notifications are enabled
            if (!$this->isAttendanceNotificationEnabled()) {
                Log::info('Attendance WhatsApp notifications are disabled');
                return false;
            }            // Load necessary relationships
            $absensi->load(['siswa.orangtua', 'siswa.kelas', 'jadwal.pelajaran']);
            
            $siswa = $absensi->siswa;
            $orangtua = $siswa->orangtua;

            // Check if parent exists and has phone number
            if (!$orangtua || empty($orangtua->no_telp)) {
                Log::warning('No parent contact found for attendance notification', [
                    'siswa_id' => $siswa->id,
                    'siswa_nama' => $siswa->nama_lengkap,
                    'has_orangtua' => !is_null($orangtua),
                    'has_no_telp' => $orangtua ? !empty($orangtua->no_telp) : false
                ]);
                return false;
            }

            // Format phone number (ensure it starts with country code)
            $phoneNumber = $this->formatPhoneNumber($orangtua->no_telp);
            
            // Generate notification message
            $message = $this->generateAttendanceMessage($absensi);
            
            // Send WhatsApp notification
            $result = $this->whatsappService->sendMessage($phoneNumber, $message);
            
            if ($result['success']) {
                Log::info('Attendance WhatsApp notification sent successfully', [
                    'siswa_id' => $siswa->id,
                    'siswa_nama' => $siswa->nama_lengkap,
                    'orangtua_nama' => $orangtua->nama_lengkap,
                    'phone_number' => $phoneNumber,
                    'status' => $absensi->status,
                    'tanggal' => $absensi->tanggal
                ]);
                return true;
            } else {
                Log::error('Failed to send attendance WhatsApp notification', [
                    'siswa_id' => $siswa->id,
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Exception in sendAttendanceNotification', [
                'siswa_id' => $absensi->siswa_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }    /**
     * Generate attendance message based on status
     */    public function generateAttendanceMessage(Absensi $absensi)
    {
        $siswa = $absensi->siswa;
        $jadwal = $absensi->jadwal;
        $mataPelajaran = $jadwal->pelajaran;
        $kelas = $siswa->kelas;
        
        $tanggal = Carbon::parse($absensi->tanggal)->format('d/m/Y');
        $waktu = $absensi->created_at->format('H:i');
        
        // Get template based on attendance status
        $templateKey = $this->getTemplateKeyByStatus($absensi->status);
        $template = Setting::getSetting($templateKey, $this->getDefaultTemplate($absensi->status));
        
        // If template is empty, use default hardcoded message
        if (empty($template)) {
            return $this->generateHardcodedMessage($absensi);
        }
        
        // Prepare variables for template replacement
        $variables = [
            'school_name' => Setting::getSetting('school_name', 'Sekolah'),
            'nama_siswa' => $siswa->nama_lengkap,
            'kelas' => $kelas->nama_kelas ?? '-',
            'tanggal' => $tanggal,
            'waktu' => $waktu,
            'status' => $this->getStatusText($absensi->status),
            'keterangan' => $absensi->keterangan ?? 'Tidak ada keterangan',
            'mata_pelajaran' => $mataPelajaran->nama_pelajaran ?? '-'
        ];
        
        // Replace template variables
        $message = $this->replaceTemplateVariables($template, $variables);
        
        return $message;
    }
    
    /**
     * Get template key based on attendance status
     */
    private function getTemplateKeyByStatus($status)
    {
        $mapping = [
            'hadir' => 'whatsapp_template_check_in',
            'terlambat' => 'whatsapp_template_late', 
            'alpha' => 'whatsapp_template_absent',
            'izin' => 'whatsapp_template_permission',
            'sakit' => 'whatsapp_template_sick'
        ];
        
        return $mapping[$status] ?? 'whatsapp_template_check_in';
    }
    
    /**
     * Get default template if none exists
     */
    private function getDefaultTemplate($status)
    {
        $defaults = [
            'hadir' => 'Halo, {nama_siswa} dari kelas {kelas} telah hadir pada {tanggal} pukul {waktu}. Status: {status}. Keterangan: {keterangan}',
            'terlambat' => 'Halo, {nama_siswa} dari kelas {kelas} terlambat pada {tanggal} pukul {waktu}. Status: {status}. Keterangan: {keterangan}',
            'alpha' => 'Halo, {nama_siswa} dari kelas {kelas} tidak hadir pada {tanggal}. Status: {status}. Keterangan: {keterangan}',
            'izin' => 'Halo, {nama_siswa} dari kelas {kelas} izin pada {tanggal}. Status: {status}. Keterangan: {keterangan}',
            'sakit' => 'Halo, {nama_siswa} dari kelas {kelas} sakit pada {tanggal}. Status: {status}. Keterangan: {keterangan}'
        ];
        
        return $defaults[$status] ?? 'Notifikasi kehadiran {nama_siswa} dari {school_name} pada {tanggal} pukul {waktu}. Status: {status}';
    }
    
    /**
     * Get human-readable status text
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat', 
            'alpha' => 'Tidak Hadir (Alpha)',
            'izin' => 'Izin',
            'sakit' => 'Sakit'
        ];
        
        return $statusTexts[$status] ?? strtoupper($status);
    }
    
    /**
     * Replace variables in template
     */
    private function replaceTemplateVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
    
    /**
     * Generate hardcoded message as fallback
     */    private function generateHardcodedMessage(Absensi $absensi)
    {
        $siswa = $absensi->siswa;
        $jadwal = $absensi->jadwal;
        $mataPelajaran = $jadwal->pelajaran;
        $kelas = $siswa->kelas;
        
        $tanggal = Carbon::parse($absensi->tanggal)->format('d/m/Y');
        $waktu = $absensi->created_at->format('H:i');
        
        $schoolName = Setting::getSetting('school_name', 'Sekolah');
        
        // Base message
        $message = "*{$schoolName}*\n";
        $message .= "Notifikasi Kehadiran Siswa\n\n";
        $message .= "👤 *Nama*: {$siswa->nama_lengkap}\n";
        $message .= "🏫 *Kelas*: " . ($kelas->nama_kelas ?? '-') . "\n";
        $message .= "📚 *Mata Pelajaran*: " . ($mataPelajaran->nama_pelajaran ?? '-') . "\n";
        $message .= "📅 *Tanggal*: {$tanggal}\n";
        $message .= "🕐 *Waktu Dicatat*: {$waktu}\n\n";

        // Status-specific message
        switch ($absensi->status) {
            case 'hadir':
                $message .= "✅ *Status*: HADIR\n";
                if ($absensi->minutes_late > 0) {
                    $message .= "⏰ *Keterangan*: " . $absensi->keterangan . "\n";
                } else {
                    $message .= "✨ *Keterangan*: Hadir tepat waktu\n";
                }
                break;
                
            case 'terlambat':
                $message .= "⚠️ *Status*: TERLAMBAT\n";
                $message .= "⏰ *Keterangan*: " . $absensi->keterangan . "\n";
                break;
                
            case 'alpha':
                $message .= "❌ *Status*: TIDAK HADIR (Alpha)\n";
                $message .= "📝 *Keterangan*: " . ($absensi->keterangan ?? 'Tidak ada keterangan') . "\n";
                break;
                
            case 'izin':
                $message .= "📄 *Status*: IZIN\n";
                $message .= "📝 *Keterangan*: " . ($absensi->keterangan ?? 'Tidak ada keterangan') . "\n";
                break;
                
            case 'sakit':
                $message .= "🏥 *Status*: SAKIT\n";
                $message .= "📝 *Keterangan*: " . ($absensi->keterangan ?? 'Tidak ada keterangan') . "\n";
                break;
                
            default:
                $message .= "ℹ️ *Status*: " . strtoupper($absensi->status) . "\n";
                $message .= "📝 *Keterangan*: " . ($absensi->keterangan ?? 'Tidak ada keterangan') . "\n";
                break;
        }

        $message .= "\n---\n";
        $message .= "Pesan otomatis dari sistem absensi sekolah.";        
        
        return $message;
    }    /**
     * Generate test attendance message for testing purposes
     */
    public function generateTestAttendanceMessage($siswa, $status, $tanggal)
    {
        $kelas = $siswa->kelas;
        
        $tanggalFormatted = Carbon::parse($tanggal)->format('d/m/Y');
        $waktu = Carbon::now()->format('H:i');
        
        // Get template based on attendance status
        $templateKey = $this->getTemplateKeyByStatus($status);
        $template = Setting::getSetting($templateKey, $this->getDefaultTemplate($status));
        
        // If template is empty, use default hardcoded message
        if (empty($template)) {
            return $this->generateTestHardcodedMessage($siswa, $status, $tanggal);
        }
        
        // Prepare variables for template replacement
        $variables = [
            'school_name' => Setting::getSetting('school_name', 'Sekolah'),
            'nama_siswa' => $siswa->nama_lengkap,
            'kelas' => $kelas->nama_kelas ?? '-',
            'tanggal' => $tanggalFormatted,
            'waktu' => $waktu,
            'status' => $this->getStatusText($status),
            'keterangan' => $this->getTestKeterangan($status),
            'mata_pelajaran' => 'Test Mata Pelajaran'
        ];
        
        // Replace template variables
        $message = $this->replaceTemplateVariables($template, $variables);
        
        return $message;
    }
    
    /**
     * Get test keterangan based on status
     */
    private function getTestKeterangan($status)
    {
        $testKeterangan = [
            'hadir' => 'Hadir tepat waktu',
            'terlambat' => 'Terlambat 15 menit',
            'alpha' => 'Tidak ada keterangan',
            'izin' => 'Izin sakit kepala',
            'sakit' => 'Demam tinggi'
        ];
        
        return $testKeterangan[$status] ?? 'Test keterangan';
    }
    
    /**
     * Generate test hardcoded message as fallback
     */
    private function generateTestHardcodedMessage($siswa, $status, $tanggal)
    {
        $kelas = $siswa->kelas;
        
        $tanggalFormatted = Carbon::parse($tanggal)->format('d/m/Y');
        $waktu = Carbon::now()->format('H:i');
        
        $schoolName = Setting::getSetting('school_name', 'Sekolah');
        
        // Base message
        $message = "*{$schoolName}*\n";
        $message .= "Notifikasi Kehadiran Siswa\n\n";
        $message .= "👤 *Nama*: {$siswa->nama_lengkap}\n";
        $message .= "🏫 *Kelas*: " . ($kelas->nama_kelas ?? '-') . "\n";
        $message .= "📚 *Mata Pelajaran*: Test Mata Pelajaran\n";
        $message .= "📅 *Tanggal*: {$tanggalFormatted}\n";
        $message .= "🕐 *Waktu Dicatat*: {$waktu}\n\n";

        // Status-specific message
        switch ($status) {
            case 'hadir':
                $message .= "✅ *Status*: HADIR\n";
                $message .= "✨ *Keterangan*: Hadir tepat waktu\n";
                break;
                
            case 'terlambat':
                $message .= "⚠️ *Status*: TERLAMBAT\n";
                $message .= "⏰ *Keterangan*: Terlambat 15 menit\n";
                break;
                
            case 'alpha':
                $message .= "❌ *Status*: TIDAK HADIR (Alpha)\n";
                $message .= "📝 *Keterangan*: Tidak ada keterangan\n";
                break;
                
            case 'izin':
                $message .= "📄 *Status*: IZIN\n";
                $message .= "📝 *Keterangan*: Izin sakit kepala\n";
                break;
                
            case 'sakit':
                $message .= "🏥 *Status*: SAKIT\n";
                $message .= "📝 *Keterangan*: Demam tinggi\n";
                break;
                
            default:
                $message .= "ℹ️ *Status*: " . strtoupper($status) . "\n";
                $message .= "📝 *Keterangan*: Tidak ada keterangan\n";
                break;
        }

        $message .= "\n---\n";
        $message .= "Pesan otomatis dari sistem absensi sekolah.";

        return $message;
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // If starts with 0, replace with +62
        if (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);
        }
        
        // If doesn't start with +, assume Indonesian number
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Check if attendance notifications are enabled
     */
    private function isAttendanceNotificationEnabled()
    {
        return (bool) Setting::getSetting('enable_whatsapp_attendance_notifications', true);
    }

    /**
     * Send bulk notifications for multiple attendance records
     */
    public function sendBulkAttendanceNotifications($attendanceRecords)
    {
        $successCount = 0;
        $failCount = 0;

        foreach ($attendanceRecords as $absensi) {
            if ($this->sendAttendanceNotification($absensi)) {
                $successCount++;
            } else {
                $failCount++;
            }
            
            // Add small delay to avoid overwhelming WhatsApp service
            usleep(500000); // 0.5 second delay
        }

        Log::info('Bulk attendance notifications completed', [
            'total' => count($attendanceRecords),
            'success' => $successCount,
            'failed' => $failCount
        ]);

        return [
            'total' => count($attendanceRecords),
            'success' => $successCount,
            'failed' => $failCount
        ];
    }
}
