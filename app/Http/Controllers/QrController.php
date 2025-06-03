<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AcademicYear;
use App\Models\JadwalMengajar;
use App\Models\Setting;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QrController extends Controller
{    // Constants for QR code settings
    const QR_VALIDITY_TYPES = ['days', 'permanent', 'daily'];
    const DEFAULT_LATE_LIMIT = 15; // minutes
    const DEFAULT_QR_SIZE = 200; // Ukuran standar untuk semua QR code
    const QR_ERROR_CORRECTION = 'H'; // High error correction untuk ketahanan scan
    
    /**
     * Display the QR management interface
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kelas = Kelas::withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();
            
        $qrGeneratedCount = Siswa::whereNotNull('qr_generated_at')->count();
        $totalStudents = Siswa::count();
        
        return view('admin.qrcode.index', compact('kelas', 'qrGeneratedCount', 'totalStudents'));
    }    /**
     * Generate QR code for a specific student attendance.
     *
     * @param  int  $siswaId
     * @return \Illuminate\Http\Response
     */
    public function generateSiswaQr($siswaId)
    {
        $siswa = Siswa::with('kelas')->findOrFail($siswaId);
        
        // Ensure student has QR token
        if (!$siswa->qr_token) {
            $this->regenerateStudentQrToken($siswa);
        }
        
        // Ambil pengaturan sekolah untuk ID card
        $logoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
        $schoolName = DB::table('settings')->where('key', 'school_name')->value('value');
        $kepsek = DB::table('settings')->where('key', 'kepala_sekolah')->value('value') ?: 'Kepala Sekolah';
        $nip = DB::table('settings')->where('key', 'nip_kepsek')->value('value') ?: '-';
                      
        return view('admin.qrcode.siswa', compact('siswa', 'logoPath', 'schoolName', 'kepsek', 'nip'));
    }
      /**
     * Generate QR data for student - DEPRECATED: Sekarang semua QR menggunakan qr_token saja
     * Fungsi ini dipertahankan untuk backward compatibility
     */
    private function generateStudentQrData(Siswa $siswa)
    {
        // Untuk konsistensi, kembalikan hanya token
        return $siswa->qr_token;
    }
    
    /**
     * Generate QR code for daily attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateDailyQr(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'sometimes|date'
        ]);
        
        $kelas = Kelas::findOrFail($request->kelas_id);
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');
          // Generate QR data with expiration time
        $qrData = $this->generateDailyQrData($kelas, $tanggal);
        
        // Using explicit SVG format to avoid any ImageMagick issues
        $qrcode = QrCode::size(400)
                      ->color(0, 51, 153)
                      ->backgroundColor(255, 255, 255, 0)
                      ->errorCorrection(self::QR_ERROR_CORRECTION)
                      ->generate($qrData);
        
        return view('admin.absensi.qrcode', compact('kelas', 'tanggal', 'qrcode'));
    }
    
    /**
     * Generate daily QR data
     */
    private function generateDailyQrData(Kelas $kelas, string $tanggal)
    {
        return json_encode([
            'type' => 'daily_attendance',
            'kelas_id' => $kelas->id,
            'kelas_name' => $kelas->nama_kelas,
            'tanggal' => $tanggal,
            'expires_at' => now()->endOfDay()->timestamp,
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Generate QR codes for all students in a class.
     *
     * @param  int  $kelasId
     * @return \Illuminate\Http\Response
     */
    public function generateClassQr($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        
        $siswa = Siswa::where('kelas_id', $kelasId)
            ->orderBy('nama_lengkap')
            ->get();
            
        // Ensure each student has a QR token
        $siswa->each(function ($student) {
            if (!$student->qr_token) {
                $this->regenerateStudentQrToken($student);
            }
        });
            
        return view('admin.kelas.qrcodes', compact('kelas', 'siswa'));
    }

    /**
     * Reset and regenerate QR code for a specific student
     */
    public function resetQrCode($siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        
        $this->regenerateStudentQrToken($siswa);
        
        return redirect()->back()
            ->with('success', 'QR Code siswa berhasil direset dan dibuat ulang.');
    }
    
    /**
     * Bulk reset QR codes for students by class
     */
    public function bulkResetQrCode(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'confirm' => 'sometimes|boolean'
        ]);
        
        $kelas = Kelas::findOrFail($request->kelas_id);
        
        // Require confirmation for bulk operations
        if (!$request->confirm) {
            return view('admin.qrcode.confirm-bulk-reset', compact('kelas'));
        }
        
        $siswa = Siswa::where('kelas_id', $kelas->id)->get();
        
        $siswa->each(function ($student) {
            $this->regenerateStudentQrToken($student);
        });
        
        return redirect()->route('admin.kelas.show', $kelas->id)
            ->with('success', "QR Code {$siswa->count()} siswa dalam kelas {$kelas->nama_kelas} berhasil direset.");
    }
    
    /**
     * Regenerate student QR token
     */    private function regenerateStudentQrToken(Siswa $siswa)
    {
        $siswa->update([
            'qr_token' => Str::random(40) . time(),
            'qr_generated_at' => now()
        ]);
    }
    
    /**
     * Show QR code validation settings form
     */    public function showQrSettings()
    {
        $settings = $this->getQrSettings();
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        // Get main system settings for late tolerance and maximum late minutes
        $mainSettings = [
            'enable_late_tolerance_system' => (bool) Setting::getSetting('enable_late_tolerance_system', true),
            'late_tolerance_minutes' => (int) Setting::getSetting('late_tolerance_minutes', 5),
            'max_late_minutes' => (int) Setting::getSetting('max_late_minutes', 30)
        ];
        
        return view('admin.qrcode.settings', compact('settings', 'academicYear', 'mainSettings'));
    }
    
    /**
     * Update QR code validation settings
     */    public function updateQrSettings(Request $request)
    {
        $validated = $request->validate([
            'qr_validity_period_type' => 'required|in:' . implode(',', self::QR_VALIDITY_TYPES),
            'qr_validity_period' => 'required_if:qr_validity_period_type,days|integer|min:0',
            'qr_auto_reset' => 'required|boolean',
            'require_active_academic_year' => 'required|boolean',
            'allow_multiple_scans' => 'required|boolean',
            'validate_by_schedule' => 'required|boolean',
        ]);
        
        // Prepare settings data
        $settings = [
            'qr_validity_period_type' => $validated['qr_validity_period_type'],
            'qr_auto_reset' => (bool)$validated['qr_auto_reset'],
            'require_active_academic_year' => (bool)$validated['require_active_academic_year'],
            'allow_multiple_scans' => (bool)$validated['allow_multiple_scans'],
            'validate_by_schedule' => (bool)$validated['validate_by_schedule'],
            // Late limit settings are now managed in main settings, not QR settings
            'enforce_late_limit' => true, // Always enforce since it's managed centrally
            'late_limit_minutes' => self::DEFAULT_LATE_LIMIT, // Kept for backward compatibility
        ];
        
        // Handle validity period based on type
        if ($validated['qr_validity_period_type'] === 'days') {
            $settings['qr_validity_period'] = (int)$validated['qr_validity_period'];
        } else {
            $settings['qr_validity_period'] = 0;
        }
        
        // Save settings
        $this->saveQrSettings($settings);
        
        return redirect()->route('admin.qrcode.settings')
            ->with('success', 'Pengaturan QR Code berhasil diperbarui.');
    }
    
    /**
     * Save QR settings to database and file
     */
    private function saveQrSettings(array $settings)
    {
        try {
            DB::transaction(function () use ($settings) {
                DB::table('settings')->updateOrInsert(
                    ['key' => 'qrcode_settings'],
                    [
                        'value' => json_encode($settings),
                        'updated_at' => now(),
                        'created_at' => DB::raw('COALESCE(created_at, NOW())')
                    ]
                );
                  // Backup to file
                Storage::put('private/qr_settings.json', json_encode($settings, JSON_PRETTY_PRINT));
            });
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get the current QR code settings
     */
    private function getQrSettings()
    {
        try {
            // Try database first
            $settings = DB::table('settings')
                ->where('key', 'qrcode_settings')
                ->value('value');
                
            if ($settings) {
                return json_decode($settings, false);
            }
            
            // Fallback to file
            if (Storage::exists('private/qr_settings.json')) {
                $fileSettings = json_decode(Storage::get('private/qr_settings.json'));
                $this->saveQrSettings((array)$fileSettings); // Migrate to database
                return $fileSettings;            }
        } catch (\Exception $e) {
            // Silent error - use defaults
        }
        
        // Return default settings if none found
        return (object)[
            'qr_validity_period_type' => 'permanent',
            'qr_validity_period' => 0,
            'qr_auto_reset' => false,
            'require_active_academic_year' => true,
            'allow_multiple_scans' => false,
            'validate_by_schedule' => true,
            'enforce_late_limit' => true,
            'late_limit_minutes' => self::DEFAULT_LATE_LIMIT,        
        ];
    }

    /**
     * Validate QR code against schedule
     */    private function validateJadwal(Siswa $siswa)
    {
        $settings = $this->getQrSettings();
        
        if (!($settings->validate_by_schedule ?? true)) {
            return ['valid' => true, 'message' => 'Validasi jadwal tidak diaktifkan'];
        }
          $now = now();
        $currentDay = $now->dayOfWeekIso;
        $currentTime = $now->format('H:i:s');
          // Find the most appropriate schedule for today's class based on current time
        // Priority: 1. Current ongoing class, 2. Next upcoming class, 3. Closest class that's finished
        $allJadwalToday = JadwalMengajar::where('hari', $currentDay)
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('jam_mulai')
            ->get();
            
        if ($allJadwalToday->isEmpty()) {
            $jadwal = null;
        } else {
            // Find the best matching schedule based on current time
            $jadwal = $this->findBestScheduleMatch($allJadwalToday, $currentTime);
        }if (!$jadwal) {
            // In development/testing mode, allow QR validation even without schedules
            if (config('app.env') === 'local' || config('app.env') === 'development' || config('app.debug', false)) {
                return [
                    'valid' => true,
                    'status' => 'hadir',
                    'minutes_late' => 0,
                    'message' => 'Kode QR berlaku (Mode Pengembangan - Tidak diperlukan jadwal)',
                    'jadwal' => null,
                    'is_valid_attendance' => true
                ];
            }
            
            return [
                'valid' => false,
                'message' => 'Tidak ada jadwal pelajaran untuk hari ini'
            ];        }
        
        // Check late limit if enabled
        if ($settings->enforce_late_limit ?? false) {
            // Use full datetime comparison but ensure we're only comparing within the same day
            $now = Carbon::now();
            $jadwalMulai = Carbon::parse($now->format('Y-m-d') . ' ' . $jadwal->jam_mulai);
              // If current time is before class start, no lateness
            if ($now->lt($jadwalMulai)) {
                return [
                    'valid' => true,
                    'status' => 'hadir',
                    'minutes_late' => 0,
                    'message' => 'Hadir tepat waktu',
                    'jadwal' => $jadwal,
                    'is_valid_attendance' => true
                ];
            }

            // Calculate minutes late using full datetime comparison
            // But add a sanity check to prevent extremely large values
            $minutesLate = $jadwalMulai->diffInMinutes($now);
              // Sanity check: if minutes late is more than 24 hours (1440 minutes), 
            // something is wrong with the calculation - default to 0
            if ($minutesLate > 1440) {
                $minutesLate = 0;
            }
              // Ensure minutes late is always an integer to prevent floating point issues
            $minutesLate = (int) $minutesLate;
            
            // Check if late tolerance system is enabled
            $toleranceSystemEnabled = (bool) \App\Models\Setting::getSetting('enable_late_tolerance_system', true);
              if (!$toleranceSystemEnabled) {
                // Tolerance system disabled - record actual lateness but mark as present
                if ($minutesLate > 0) {
                    return [
                        'valid' => true,
                        'status' => 'hadir',
                        'minutes_late' => $minutesLate,
                        'message' => "Hadir dengan keterlambatan $minutesLate menit (sistem toleransi dinonaktifkan)",
                        'jadwal' => $jadwal,
                        'is_valid_attendance' => true
                    ];
                } else {
                    return [
                        'valid' => true,
                        'status' => 'hadir',
                        'minutes_late' => 0,
                        'message' => "Hadir tepat waktu",
                        'jadwal' => $jadwal,
                        'is_valid_attendance' => true
                    ];
                }
            }
              // Use database settings for tolerance limits (only if tolerance system is enabled)
            $lateLimit = \App\Models\Setting::getSetting('late_tolerance_minutes', 0); // No tolerance - any lateness is recorded
            $maxLateLimit = \App\Models\Setting::getSetting('max_late_minutes', 30); // Default 30 minutes for max limit
            // Students can always enter class, but mark as late if beyond tolerance
            // Any lateness beyond tolerance (0 minutes) is marked as late
            if ($minutesLate > $lateLimit) {
                $status = 'terlambat';
                $keterangan = "Terlambat $minutesLate menit";
                  if ($minutesLate > $maxLateLimit) {
                    $keterangan .= " (melebihi batas maksimum $maxLateLimit menit, tetapi tetap diperbolehkan masuk)";
                }
                
                return [
                    'valid' => true,
                    'status' => $status,
                    'minutes_late' => $minutesLate,
                    'message' => $keterangan,
                    'jadwal' => $jadwal,
                    'is_valid_attendance' => true
                ];
            }
            
            // Within tolerance limit - on time
            return [
                'valid' => true,
                'status' => 'hadir',
                'minutes_late' => $minutesLate,
                'message' => $minutesLate > 0 ? "Masih dalam batas toleransi keterlambatan ($lateLimit menit)" : "Hadir tepat waktu",
                'jadwal' => $jadwal,
                'is_valid_attendance' => true
            ];
        }
        
        // Default response for non-late attendance
        return [
            'valid' => true,
            'status' => 'hadir',
            'minutes_late' => 0,
            'message' => 'QR Code valid untuk jadwal saat ini',
            'jadwal' => $jadwal,
            'is_valid_attendance' => true        
        ];
    }    /**
     * Validate QR code for internal use (returns raw array)
     */
    public function validateQrCodeData($qrToken)
    {
        return $this->performQrValidation($qrToken);
    }

    /**
     * Validate QR code (public endpoint, returns appropriate response)
     */    public function validateQrCode($qrToken)
    {
        // Handle scanner view request
        if ($qrToken === 'scan') {
            return view('admin.qrcode.validate');
        }        // Validate and sanitize input
        $cleanToken = $this->sanitizeQrToken($qrToken);
        if (!$cleanToken) {
            return $this->validationResponse([
                'valid' => false,
                'message' => 'Format QR Code tidak valid',
                'siswa' => null
            ]);
        }        // Perform validation with error handling
        try {
            $validationData = $this->performQrValidation($cleanToken);
            return $this->validationResponse($validationData);
        } catch (\Exception $e) {
            return $this->validationResponse([
                'valid' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi QR Code. Silakan coba lagi.',
                'siswa' => null
            ]);
        }
    }    /**
     * Sanitize and validate QR token format
     */
    private function sanitizeQrToken($token)
    {
        // Remove any whitespace and special characters
        $clean = trim($token);
        
        // Check if token is valid QR token format (40 chars + timestamp or JSON)
        if (strlen($clean) >= 40 && preg_match('/^[a-zA-Z0-9]+/', $clean)) {
            return $clean;
        }
          // Check if it's a valid NISN (numeric string, typically 10-20 characters)
        if (strlen($clean) >= 5 && strlen($clean) <= 20 && preg_match('/^[0-9]+$/', $clean)) {
            return $clean; // Return NISN for processing
        }
        
        // Try to decode as JSON
        $decoded = json_decode($clean, true);
        if ($decoded && isset($decoded['qr_token'])) {
            return $decoded['qr_token'];
        }
        
        return false;
    }

    /**
     * Core QR validation logic
     */
    private function performQrValidation($qrToken)
    {        // Process and extract token from different formats
        $processed_token = $this->processQrToken($qrToken);
        
        if ($processed_token !== $qrToken) {
            $qrToken = $processed_token;
        }        // For testing purposes - if no students have QR tokens, we'll allow test tokens
        if (strpos($qrToken, 'test_') === 0) {
            if (env('APP_ENV') === 'local' || env('APP_ENV') === 'development') {
                // In development environment, we'll create a fake student
                
                // Return test data
                return [
                    'valid' => true,
                    'message' => 'QR Code valid (TEST MODE)',
                    'siswa' => [
                        'id' => 999,
                        'nama' => 'TEST SISWA',                        'nisn' => 'TEST-NISN-' . substr($qrToken, 5),
                        'kelas' => 'Kelas Test',
                        'status' => 'Aktif',
                        'qr_generated_at' => now()->format('d/m/Y H:i'),
                        'is_test' => true
                    ]
                ];
            }
        }        // Normal validation path
        $siswa = Siswa::with('kelas')->where('qr_token', $qrToken)->first();
        
        // If not found by QR token, try NISN lookup (for manual input)
        if (!$siswa && is_numeric($qrToken)) {
            $siswa = Siswa::with('kelas')->where('nisn', $qrToken)->first();
            
            if ($siswa) {
                // Generate QR token if student doesn't have one
                if (!$siswa->qr_token) {
                    $this->regenerateStudentQrToken($siswa);
                    $siswa->refresh(); // Reload the model to get the new token
                }
            }
        }
        
        // Check if we have any students with QR tokens at all
        $hasAnyQrStudents = Siswa::whereNotNull('qr_token')->exists();
          if (!$siswa) {
            if (!$hasAnyQrStudents) {
                return [
                    'valid' => false,
                    'message' => 'Tidak ada siswa dengan QR Code yang terdaftar. Silakan generate QR Code untuk siswa terlebih dahulu.',
                    'setup_required' => true
                ];
            }
            
            return [
                'valid' => false,
                'message' => 'QR Code tidak valid atau tidak ditemukan'
            ];        }

        $settings = $this->getQrSettings();
        $validationResult = $this->validateStudentQr($siswa, $settings);
        
        return $validationResult;
    }    /**
     * Perform all QR validation checks
     */    private function validateStudentQr(Siswa $siswa, $settings)
    {
        // Note: Student status check removed as status column doesn't exist in current schema
        // Students are considered active if they have a record in the database
        
        // Use the enhanced QR validity check
        $validityCheck = $this->checkQrValidity($siswa);
        if (!$validityCheck['valid']) {
            return $validityCheck;
        }
        
        // Validate against schedule
        $scheduleValidation = $this->validateJadwal($siswa);
        if (!$scheduleValidation['valid']) {
            return array_merge($scheduleValidation, ['siswa' => null]);        }
        
        // All validations passed
        $baseResult = [
            'valid' => true,
            'message' => 'QR Code valid - Data siswa berhasil diverifikasi',
            'siswa' => [
                'id' => $siswa->id,
                'nama' => $siswa->nama_lengkap,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas->nama_kelas,
                'status' => 'Aktif',
                'qr_generated_at' => $siswa->qr_generated_at ? 
                    (is_string($siswa->qr_generated_at) ? $siswa->qr_generated_at : $siswa->qr_generated_at->format('d/m/Y H:i')) :
                    'N/A',
                'jenis_kelamin' => $siswa->jenis_kelamin ?? null,
                'tanggal_lahir' => $siswa->tanggal_lahir ?? null
            ],
            'validation_time' => now()->toISOString(),
            'qrCode' => $siswa->qr_token
        ];

        // Merge schedule validation result with base result
        return array_merge($baseResult, $scheduleValidation);
    }/**
     * Return validation response in appropriate format
     */    private function validationResponse(array $data)
    {
        // Add HTTP status code based on validation result - helps AJAX client handle errors better
        $statusCode = $data['valid'] ? 200 : 422;
        
        // Add a debug trace ID to help correlate server and client logs
        $data['trace_id'] = Str::random(8);
        
        if (request()->ajax()) {
            return response()->json($data, $statusCode);
        }
        
        return view('admin.qrcode.validate-result', [
            'data' => $data,
            'scanAgainUrl' => route('admin.qrcode.validate', ['qrToken' => 'scan'])
        ]);
    }/**
     * Display print preview for class QR codes
     *
     * @param int $kelasId
     * @return \Illuminate\Http\Response
     */
    public function printPreviewQrCodes($kelasId)
    {
        // Ambil data kelas
        $kelas = Kelas::with(['siswa' => function($query) {
            $query->orderBy('nama_lengkap', 'asc');
        }])->findOrFail($kelasId);

        // Ambil pengaturan sekolah
        $logoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
        $schoolName = DB::table('settings')->where('key', 'school_name')->value('value');
        $kepsek = DB::table('settings')->where('key', 'kepala_sekolah')->value('value') ?: 'Kepala Sekolah';
        $nip = DB::table('settings')->where('key', 'nip_kepsek')->value('value') ?: '-';

        // Generate QR code untuk siswa yang belum memiliki - GUNAKAN METODE YANG SAMA
        foreach($kelas->siswa as $siswa) {
            if(!$siswa->qr_token) {
                $this->regenerateStudentQrToken($siswa);
            }
        }

        return view('admin.kelas.print-qrcodes', compact('kelas', 'logoPath', 'schoolName', 'kepsek', 'nip'));
    }
    
    /**
     * Enhanced QR token processing with better format detection
     * 
     * @param string $qrToken
     * @return string
     */    private function processQrToken($qrToken)
    {
        // Handle URL encoding issues - sometimes QR codes may contain special characters
        if (strpos($qrToken, '%') !== false) {
            $decoded = urldecode($qrToken);
            $qrToken = $decoded;
        }
        
        // If it's just a plain token string, return it as is
        if (!str_starts_with($qrToken, '{') && !str_starts_with($qrToken, '[')) {
            return $qrToken;
        }
        
        try {
            // Try to parse as JSON
            $parsedData = json_decode($qrToken, true);
              // If parsing failed, return original
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $qrToken;
            }
            
            // Various formats we might expect
            if (isset($parsedData['qr_token'])) {
                return $parsedData['qr_token'];
            }
              // Try student/nisn lookup
            else if (isset($parsedData['student_id']) || isset($parsedData['siswa_id']) || isset($parsedData['nisn'])) {
                // This appears to be our student QR format, try to find student
                $query = Siswa::query();
                
                if (isset($parsedData['student_id']) || isset($parsedData['siswa_id'])) {
                    $studentId = $parsedData['student_id'] ?? $parsedData['siswa_id'];
                    $query->where('id', $studentId);
                }
                
                if (isset($parsedData['nisn'])) {
                    $query->orWhere('nisn', $parsedData['nisn']);
                }
                
                $siswa = $query->first();
                
                if ($siswa && $siswa->qr_token) {
                    return $siswa->qr_token;
                }
            }
              // Try secondary identifiers (name, class)
            else if (isset($parsedData['name']) && isset($parsedData['class'])) {
                // Attempt to find by name and class
                $siswa = Siswa::whereRaw('LOWER(nama_lengkap) = ?', [strtolower($parsedData['name'])])
                    ->whereHas('kelas', function($q) use ($parsedData) {
                        $q->whereRaw('LOWER(nama_kelas) = ?', [strtolower($parsedData['class'])]);
                    })
                    ->first();
                
                if ($siswa && $siswa->qr_token) {
                    return $siswa->qr_token;
                }            }
            
            // If this looks like a test token in any format, return it explicitly
            if (isset($parsedData['is_test']) || 
                (isset($parsedData['name']) && stripos($parsedData['name'], 'test') !== false) ||
                (isset($parsedData['nisn']) && stripos($parsedData['nisn'], 'test') !== false)) {
                
                $testToken = 'test_token_' . time();
                return $testToken;
            }
              return $qrToken;
            
        } catch (\Exception $e) {
            return $qrToken;
        }
    }    /**
     * Generate a test QR code for development purposes
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateTestQr(Request $request)
    {
        if (env('APP_ENV') !== 'local' && env('APP_ENV') !== 'development') {
            abort(404);
        }
        
        $format = $request->get('format', 'json_full');
        $testToken = 'test_token_' . time();
        $qrData = '';
        
        switch ($format) {
            case 'raw_token':
                // Just a token string
                $qrData = $testToken;
                break;
                
            case 'json_token_only':
                // JSON with only token
                $qrData = json_encode([
                    'qr_token' => $testToken
                ]);
                break;
                
            case 'json_no_token':
                // JSON without token
                $qrData = json_encode([
                    'student_id' => 999,
                    'nisn' => 'TEST-NISN-123',
                    'name' => 'Test Student',
                    'class' => 'Test Class',
                    'timestamp' => now()->timestamp
                ]);
                break;
                
            case 'invalid':
                // Invalid format to test error handling
                $qrData = 'Invalid-QR-Data-' . time();
                break;
                
            default:
                // Full JSON with all fields
                $qrData = json_encode([
                    'student_id' => 999,
                    'nisn' => 'TEST-NISN-123',
                    'name' => 'Test Student',
                    'class' => 'Test Class',
                    'qr_token' => $testToken,
                    'timestamp' => now()->timestamp
                ]);
                break;
        }
          // Generate the QR code (always SVG to avoid ImageMagick errors)
        $qrcode = QrCode::size(300)
            ->errorCorrection(self::QR_ERROR_CORRECTION)
            ->generate($qrData);
        
        return view('admin.qrcode.test', [
            'qrcode' => $qrcode,
            'testToken' => $testToken,
            'qrData' => $qrData,
            'format' => $format
        ]);
    }

    /**
     * Generate standardized QR code for student
     * 
     * @param Siswa $siswa
     * @param int $size
     * @return string
     */
    public static function generateStandardQrCode(Siswa $siswa, int $size = null)
    {
        $size = $size ?: self::DEFAULT_QR_SIZE;
        
        // Pastikan siswa memiliki QR token
        if (!$siswa->qr_token) {
            $siswa->update([
                'qr_token' => Str::random(40) . time(),
                'qr_generated_at' => now()
            ]);
        }
        
        return QrCode::size($size)
                    ->errorCorrection(self::QR_ERROR_CORRECTION)
                    ->generate($siswa->qr_token);
    }
    
    /**
     * Enhanced QR validity check with better error handling
     */
    private function checkQrValidity(Siswa $siswa)
    {        $settings = $this->getQrSettings();
          // Check if QR was ever generated
        if (!$siswa->qr_generated_at) {
            return [
                'valid' => false,
                'message' => 'QR Code belum pernah di-generate untuk siswa ini',
                'siswa' => null
            ];
        }
        
        // Check validity period based on type
        switch ($settings->qr_validity_period_type ?? 'permanent') {
            case 'days':
                if ($settings->qr_validity_period > 0) {                    $expiryDate = $siswa->qr_generated_at->addDays($settings->qr_validity_period);
                    if ($expiryDate->isPast()) {
                        return [
                            'valid' => false,
                            'message' => "QR Code sudah kadaluarsa (berlaku {$settings->qr_validity_period} hari)",
                            'siswa' => null
                        ];
                    }
                }
                break;
                
            case 'daily':                // QR code is only valid for today
                if (!$siswa->qr_generated_at->isToday()) {
                    return [
                        'valid' => false,
                        'message' => 'QR Code harian sudah kadaluarsa (hanya berlaku untuk hari ini)',
                        'siswa' => null
                    ];
                }
                break;
                  case 'permanent':
            default:
                // QR code never expires
                break;
        }
          // Check if academic year is required and active
        if (($settings->require_active_academic_year ?? true) && 
            !AcademicYear::where('is_active', true)->exists()) {
            return [
                'valid' => false,
                'message' => 'Tidak ada tahun akademik yang aktif',
                'siswa' => null
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'QR Code valid'
        ];
    }
    
    /**
     * Find the best matching schedule based on current time
     * Priority: 1. Current ongoing class, 2. Next upcoming class, 3. Most recently finished class
     */
    private function findBestScheduleMatch($allJadwalToday, $currentTime)
    {
        if ($allJadwalToday->isEmpty()) {
            return null;
        }
        
        $currentTimeCarbon = \Carbon\Carbon::createFromFormat('H:i:s', $currentTime);
        $ongoingClasses = [];
        $upcomingClasses = [];
        $finishedClasses = [];
        
        foreach ($allJadwalToday as $jadwal) {
            $jamMulai = \Carbon\Carbon::createFromFormat('H:i:s', $jadwal->jam_mulai);
            $jamSelesai = \Carbon\Carbon::createFromFormat('H:i:s', $jadwal->jam_selesai);
            
            // Check if current time is within class duration (ongoing class)
            if ($currentTimeCarbon->between($jamMulai, $jamSelesai)) {
                $ongoingClasses[] = $jadwal;
            }            // Check if class hasn't started yet (upcoming class)
            elseif ($currentTimeCarbon->lt($jamMulai)) {
                $upcomingClasses[] = [
                    'jadwal' => $jadwal,
                    'time_diff' => $currentTimeCarbon->diffInMinutes($jamMulai)  // Fixed: current to start
                ];
            }
            // Class has finished (finished class)
            else {
                $finishedClasses[] = [
                    'jadwal' => $jadwal,
                    'time_diff' => $jamSelesai->diffInMinutes($currentTimeCarbon)  // Fixed: end to current
                ];
            }
        }
        
        // Priority 1: Return any ongoing class (prefer the first one if multiple)
        if (!empty($ongoingClasses)) {
            return $ongoingClasses[0];
        }
        
        // Priority 2: Return the closest upcoming class
        if (!empty($upcomingClasses)) {
            // Sort by time difference (closest first)
            usort($upcomingClasses, function($a, $b) {
                return $a['time_diff'] <=> $b['time_diff'];
            });
            return $upcomingClasses[0]['jadwal'];
        }
        
        // Priority 3: Return the most recently finished class
        if (!empty($finishedClasses)) {
            // Sort by time difference (most recent first = smallest time difference)
            usort($finishedClasses, function($a, $b) {
                return $a['time_diff'] <=> $b['time_diff'];
            });
            return $finishedClasses[0]['jadwal'];
        }
        
        // Fallback: return the first schedule if no logic matches
        return $allJadwalToday->first();
    }

    /**
     * Display QR Analytics Dashboard
     * 
     * @return \Illuminate\Http\Response
     */
    public function analytics()
    {
        // Get statistics
        $totalStudents = Siswa::count();
        $totalGenerated = Siswa::whereNotNull('qr_generated_at')->count();
        $validQrCodes = Siswa::whereNotNull('qr_token')->count();
        
        // Calculate expired QR codes based on settings
        $qrSettings = $this->getQrSettings();
        $expiredCount = 0;
        
        if ($qrSettings->qr_validity_period_type === 'days' && $qrSettings->qr_validity_period > 0) {
            $expiryDate = Carbon::now()->subDays($qrSettings->qr_validity_period);
            $expiredCount = Siswa::whereNotNull('qr_generated_at')
                ->where('qr_generated_at', '<', $expiryDate)
                ->count();
        }
        
        // Get class-wise statistics
        $classStats = Kelas::withCount(['siswa'])
            ->orderBy('nama_kelas')
            ->get()
            ->map(function($kelas) use ($qrSettings) {
                $generated = Siswa::where('kelas_id', $kelas->id)
                    ->whereNotNull('qr_generated_at')
                    ->count();
                    
                $hasQr = Siswa::where('kelas_id', $kelas->id)
                    ->whereNotNull('qr_token')
                    ->count();
                
                // Calculate expired QR for this class
                $expired = 0;
                if ($qrSettings->qr_validity_period_type === 'days' && $qrSettings->qr_validity_period > 0) {
                    $expiryDate = Carbon::now()->subDays($qrSettings->qr_validity_period);
                    $expired = Siswa::where('kelas_id', $kelas->id)
                        ->whereNotNull('qr_generated_at')
                        ->where('qr_generated_at', '<', $expiryDate)
                        ->count();
                }
                    
                return [
                    'kelas_id' => $kelas->id,
                    'kelas_name' => $kelas->nama_kelas,
                    'total_students' => $kelas->siswa_count,
                    'qr_generated' => $generated,
                    'qr_valid' => $hasQr,
                    'qr_expired' => $expired,
                    'completion_percentage' => $kelas->siswa_count > 0 ? round(($hasQr / $kelas->siswa_count) * 100, 1) : 0
                ];
            });
        
        // Get recent QR activities (last 50)
        $recentActivities = Siswa::whereNotNull('qr_generated_at')
            ->with('kelas:id,nama_kelas')
            ->orderBy('qr_generated_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($siswa) {
                $kelasName = ($siswa->kelas && $siswa->kelas->nama_kelas) ? $siswa->kelas->nama_kelas : 'Unknown';
                return [
                    'title' => 'QR Code Generated',
                    'description' => "QR code generated untuk {$siswa->nama} ({$kelasName})",
                    'time' => Carbon::parse($siswa->qr_generated_at)->diffForHumans(),
                    'icon' => 'qrcode',
                    'color' => 'success'
                ];
            });
        
        // System health metrics
        $healthMetrics = [
            'qr_coverage' => $totalStudents > 0 ? round(($validQrCodes / $totalStudents) * 100, 1) : 0,
            'generation_rate' => $totalStudents > 0 ? round(($totalGenerated / $totalStudents) * 100, 1) : 0,
            'expired_rate' => $validQrCodes > 0 ? round(($expiredCount / $validQrCodes) * 100, 1) : 0
        ];
        
        // System health check
        $health = [
            'academic_year' => AcademicYear::where('is_active', true)->exists(),
            'qr_settings' => !empty($qrSettings->qr_validity_period),
            'schedules' => JadwalMengajar::count() > 0,
            'storage' => Storage::disk('public')->exists('qrcodes')
        ];
        
        return view('admin.qrcode.analytics', compact(
            'totalStudents', 
            'totalGenerated', 
            'validQrCodes', 
            'expiredCount',
            'classStats',
            'recentActivities',
            'healthMetrics',
            'health',
            'qrSettings'
        ));
    }

    /**
     * Generate QR codes for all students who don't have one
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAllMissingQR()
    {
        try {
            $studentsWithoutQR = Siswa::whereNull('qr_token')->get();
            $generated = 0;
            
            foreach ($studentsWithoutQR as $siswa) {
                $this->regenerateStudentQrToken($siswa);
                $generated++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "QR code berhasil digenerate untuk {$generated} siswa",
                'generated_count' => $generated
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh expired QR codes
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshExpiredQR()
    {
        try {
            $qrSettings = $this->getQrSettings();
            $refreshed = 0;
            
            if ($qrSettings->qr_validity_period_type === 'days' && $qrSettings->qr_validity_period > 0) {
                $expiryDate = Carbon::now()->subDays($qrSettings->qr_validity_period);
                $expiredStudents = Siswa::whereNotNull('qr_generated_at')
                    ->where('qr_generated_at', '<', $expiryDate)
                    ->get();
                
                foreach ($expiredStudents as $siswa) {
                    $this->regenerateStudentQrToken($siswa);
                    $refreshed++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "QR code kadaluarsa berhasil diperbarui untuk {$refreshed} siswa",
                'refreshed_count' => $refreshed
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download analytics report
     * 
     * @return \Illuminate\Http\Response
     */
    public function downloadReport()
    {
        $reportData = [
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'total_students' => Siswa::count(),
            'total_generated' => Siswa::whereNotNull('qr_generated_at')->count(),
            'valid_qr_codes' => Siswa::whereNotNull('qr_token')->count(),
            'class_breakdown' => Kelas::withCount('siswa')
                ->get()
                ->map(function($kelas) {
                    $generated = Siswa::where('kelas_id', $kelas->id)
                        ->whereNotNull('qr_generated_at')
                        ->count();
                    $hasQr = Siswa::where('kelas_id', $kelas->id)
                        ->whereNotNull('qr_token')
                        ->count();
                    
                    return [
                        'kelas' => $kelas->nama_kelas,
                        'total_siswa' => $kelas->siswa_count,
                        'qr_generated' => $generated,
                        'has_valid_qr' => $hasQr,
                        'percentage' => $kelas->siswa_count > 0 ? round(($hasQr / $kelas->siswa_count) * 100, 1) : 0
                    ];
                })
        ];
        
        $filename = 'qr_analytics_report_' . Carbon::now()->format('Y-m-d_H-i-s') . '.json';
        
        return response()->json($reportData)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }
}