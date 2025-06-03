<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JurnalMengajar;
use App\Models\Setting;
use App\Services\AttendanceNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AbsensiController extends Controller
{
    private $attendanceNotificationService;

    public function __construct(AttendanceNotificationService $attendanceNotificationService)
    {
        $this->attendanceNotificationService = $attendanceNotificationService;
    }
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('guru.login')->with('error', 'Anda harus login terlebih dahulu');
        }
        
        $today = Carbon::now()->toDateString();
        $dayOfWeek = Carbon::now()->dayOfWeekIso;
        
        $jadwalHariIni = JadwalMengajar::where('guru_id', $guru->id)
            ->where('hari', $dayOfWeek)
            ->with(['kelas', 'pelajaran'])
            ->orderBy('jam_mulai')
            ->get();
        
        $absensiHariIni = Absensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $today)
            ->get()
            ->keyBy('jadwal_id');
          // Get unique attendance sessions by date and jadwal_id
        $riwayatAbsensi = Absensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', '<=', $today)
            ->whereDate('tanggal', '>=', Carbon::now()->subDays(7))
            ->select('tanggal', 'jadwal_id', DB::raw('MIN(id) as id'))
            ->groupBy('tanggal', 'jadwal_id')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Fetch full records for the unique ids
        $riwayatIds = $riwayatAbsensi->pluck('id');
        $riwayatAbsensi = Absensi::whereIn('id', $riwayatIds)
            ->with(['jadwal.kelas', 'jadwal.pelajaran'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru.absensi.index', compact('jadwalHariIni', 'absensiHariIni', 'riwayatAbsensi'));
    }

    public function create()
    {
        $guru = Auth::guard('guru')->user();
        $jadwal = JadwalMengajar::where('guru_id', $guru->id)
                      ->with(['kelas', 'pelajaran'])
                      ->orderBy('hari')
                      ->orderBy('jam_mulai')
                      ->get();
        
        $today = now()->dayOfWeekIso;
        $todayJadwal = $jadwal->filter(function ($item) use ($today) {
            return $item->hari == $today;
        });
        
        return view('guru.absensi.create', compact('jadwal', 'todayJadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_mengajar,id',
            'tanggal' => 'required|date',
            'siswa' => 'required|array',
            'siswa.*.id' => 'required|exists:siswas,id',
            'siswa.*.status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'siswa.*.keterangan' => 'nullable|string',
        ]);

        $guru = Auth::guard('guru')->user();
        $tanggal = $request->tanggal;
        $jadwalId = $request->jadwal_id;
        
        if (Absensi::where('tanggal', $tanggal)
            ->where('jadwal_id', $jadwalId)
            ->where('guru_id', $guru->id)
            ->exists()) {
            return redirect()->back()->with('error', 'Absensi untuk jadwal dan tanggal ini sudah ada!');
        }
        
        DB::beginTransaction();
        try {
            foreach ($request->siswa as $data) {
                $minutesLate = 0;
                if ($data['status'] === 'terlambat') {
                    preg_match('/(\d+)/', $data['keterangan'], $matches);
                    $minutesLate = $matches[1] ?? 0;
                }

                Absensi::create([
                    'tanggal' => $tanggal,
                    'jadwal_id' => $jadwalId,
                    'guru_id' => $guru->id,
                    'siswa_id' => $data['id'],
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'minutes_late' => $minutesLate,
                ]);
            }
            
            DB::commit();
            return redirect()->route('guru.absensi.index')->with('success', 'Absensi berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('guru.login')->with('error', 'Anda harus login terlebih dahulu');
        }
        
        $absensi = Absensi::find($id);
        if (!$absensi || $absensi->guru_id != $guru->id) {
            return redirect()->route('guru.absensi.index')->with('error', 'Data tidak ditemukan atau tidak memiliki akses');
        }
        
        $jadwal = $absensi->jadwal ?? (object) [
            'jam_ke' => '-',
            'jam_mulai' => '-',
            'jam_selesai' => '-',
            'pelajaran' => (object) ['nama_pelajaran' => '-']
        ];
        
        $absensiDetail = Absensi::where('jadwal_id', $absensi->jadwal_id)
                 ->where('tanggal', $absensi->tanggal)
                 ->where('guru_id', $guru->id)
                 ->with('siswa')
                 ->get();
        
        // Calculate total students in the class
        $totalSiswa = 0;
        if ($jadwal && isset($jadwal->kelas_id)) {
            $totalSiswa = Siswa::where('kelas_id', $jadwal->kelas_id)->count();
        }
        
        $stats = [
            'hadir' => $absensiDetail->where('status', 'hadir')->count(),
            'terlambat' => $absensiDetail->where('status', 'terlambat')->count(),
            'izinSakit' => $absensiDetail->whereIn('status', ['izin', 'sakit'])->count(),
            'alpha' => $absensiDetail->where('status', 'alpha')->count()
        ];
        $stats['total'] = array_sum($stats);

        // Extract individual variables for the view
        $hadir = $stats['hadir'];
        $terlambat = $stats['terlambat'];
        $izinSakit = $stats['izinSakit'];
        $alpha = $stats['alpha'];

        return view('guru.absensi.show', compact('absensi', 'absensiDetail', 'jadwal', 'stats', 'totalSiswa', 'hadir', 'terlambat', 'izinSakit', 'alpha'));
    }

    public function edit($id)
    {
        $tanggal = $id;
        $guru = Auth::guard('guru')->user();
        
        $absensi = Absensi::where('tanggal', $tanggal)
                  ->where('guru_id', $guru->id)
                  ->with(['siswa', 'jadwal', 'jadwal.kelas', 'jadwal.pelajaran'])
                  ->get();
        
        if ($absensi->isEmpty()) {
            return redirect()->route('guru.absensi.index')->with('error', 'Data absensi tidak ditemukan');
        }
        
        return view('guru.absensi.edit', compact('absensi', 'tanggal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa' => 'required|array',
            'siswa.*.id' => 'required|exists:absensis,id',
            'siswa.*.status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'siswa.*.keterangan' => 'nullable|string',
        ]);
        
        $guru = Auth::guard('guru')->user();
        
        DB::beginTransaction();
        try {
            foreach ($request->siswa as $data) {
                $absensi = Absensi::find($data['id']);
                if ($absensi->guru_id != $guru->id) continue;

                $minutesLate = 0;
                if ($data['status'] === 'terlambat') {
                    preg_match('/(\d+)/', $data['keterangan'], $matches);
                    $minutesLate = $matches[1] ?? 0;
                }

                $absensi->update([
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'minutes_late' => $minutesLate,
                ]);
            }
            
            DB::commit();
            return redirect()->route('guru.absensi.show', $id)->with('success', 'Absensi berhasil diperbarui!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $guru = Auth::guard('guru')->user();
        
        DB::beginTransaction();
        try {
            $deleted = Absensi::where('tanggal', $id)
                      ->where('guru_id', $guru->id)
                      ->delete();
            
            if (!$deleted) {
                return redirect()->route('guru.absensi.index')->with('error', 'Tidak ada data yang dihapus');
            }
            
            DB::commit();
            return redirect()->route('guru.absensi.index')->with('success', 'Data absensi berhasil dihapus!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
      public function takeAttendance($jadwalId)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('guru.login')->with('error', 'Anda harus login terlebih dahulu');
        }
        
        $jadwal = JadwalMengajar::with('kelas')->find($jadwalId);
        if (!$jadwal || !$jadwal->kelas) {
            return redirect()->route('guru.absensi.index')
                   ->with('error', 'Jadwal atau kelas tidak ditemukan');
        }
        
        $siswa = Siswa::where('kelas_id', $jadwal->kelas->id)->get();
        if ($siswa->isEmpty()) {
            return redirect()->route('guru.absensi.index')
                   ->with('error', 'Tidak ada siswa di kelas ini');
        }
          $tanggal = now()->toDateString();
        
        // Check if this guru is assigned to this schedule
        if ($jadwal->guru_id !== $guru->id) {
            return redirect()->route('guru.absensi.index')
                   ->with('error', 'Anda tidak memiliki akses ke jadwal ini');
        }
        
        $absensiDetail = Absensi::where('tanggal', $tanggal)
            ->where('jadwal_id', $jadwalId)
            ->whereNotNull('siswa_id')
            ->with(['siswa', 'jadwal.pelajaran'])
            ->get();
            
        $stats = [
            'total' => $siswa->count(),
            'hadir' => $absensiDetail->whereIn('status', ['hadir', 'terlambat'])->count()
        ];

        return view('guru.absensi.takeAttendance', compact(
            'jadwal', 'siswa', 'absensiDetail', 'stats'
        ));
    }

    public function getAttendanceData($jadwalId)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $jadwal = JadwalMengajar::with('kelas')->find($jadwalId);
        if (!$jadwal || $jadwal->guru_id !== $guru->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        $tanggal = now()->toDateString();
        $siswa = Siswa::where('kelas_id', $jadwal->kelas->id)->get();
        
        $absensiDetail = Absensi::where('tanggal', $tanggal)
            ->where('jadwal_id', $jadwalId)
            ->whereNotNull('siswa_id')
            ->with(['siswa', 'jadwal.pelajaran'])
            ->get();
            
        $stats = [
            'total' => $siswa->count(),
            'hadir' => $absensiDetail->whereIn('status', ['hadir', 'terlambat'])->count()
        ];
        
        // Format attendance data for JSON response
        $attendanceList = $absensiDetail->map(function($detail) {
            $minutes = abs($detail->minutes_late ?? 0);
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            
            if ($hours > 0 && $remainingMinutes > 0) {
                $timeText = $hours . ' jam ' . $remainingMinutes . ' menit';
            } elseif ($hours > 0) {
                $timeText = $hours . ' jam';
            } else {
                $timeText = $remainingMinutes . ' menit';
            }
            
            $statusBadge = '';
            if ($detail->status == 'hadir' && (!$detail->minutes_late || $detail->minutes_late <= 0)) {
                $statusBadge = '<span class="badge badge-success">Hadir</span>';
            } elseif ($detail->status == 'terlambat' || ($detail->status == 'hadir' && $detail->minutes_late > 0)) {
                $statusBadge = '<span class="badge badge-warning">Terlambat (' . $timeText . ')</span>';
            } elseif ($detail->status == 'izin') {
                $statusBadge = '<span class="badge badge-info">Izin</span>';
            } elseif ($detail->status == 'sakit') {
                $statusBadge = '<span class="badge badge-warning">Sakit</span>';
            } else {
                $statusBadge = '<span class="badge badge-danger">Alpha</span>';
            }
            
            if ($detail->keterangan && !in_array($detail->status, ['hadir', 'terlambat'])) {
                $statusBadge .= '<small class="d-block text-muted mt-1">' . $detail->keterangan . '</small>';
            }
            
            return [
                'nisn' => $detail->siswa->nisn ?? '-',
                'nama' => $detail->siswa->nama_lengkap ?? 'Unknown',
                'status' => $statusBadge,
                'kode_pelajaran' => $detail->jadwal->pelajaran->kode_pelajaran ?? 'N/A',
                'waktu' => $detail->created_at->format('H:i:s'),
                'row_class' => $detail->status == 'hadir' ? 'table-success' : ($detail->status == 'alpha' ? 'table-danger' : ($detail->status == 'terlambat' ? 'table-warning' : 'table-info'))
            ];
        });
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'attendance' => $attendanceList,
            'percentage' => $stats['total'] > 0 ? round(($stats['hadir']/$stats['total']) * 100) : 0
        ]);
    }

    public function updateStatus(Request $request, $detailId)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('guru.login')->with('error', 'Anda harus login terlebih dahulu');
        }
        
        $request->validate([
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'keterangan' => 'nullable|string',
        ]);
        
        $absensi = Absensi::findOrFail($detailId);
        if ($absensi->guru_id != $guru->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah data ini');
        }        $minutesLate = 0;
        $keterangan = $request->keterangan;
        
        // Check for inconsistency - if keterangan mentions "terlambat" but status isn't "terlambat"
        if (stripos($keterangan, 'terlambat') !== false && $request->status !== 'terlambat') {
            $request->status = 'terlambat';
        }
        
        // Extract minutes late if status is "terlambat"
        if ($request->status === 'terlambat') {
            preg_match('/(\d+[\.\d+]*)/', $keterangan, $matches);
            $minutesLate = $matches[1] ?? 0;
              // Ensure keterangan reflects status
            if (stripos($keterangan, 'terlambat') === false) {
                $keterangan = $this->formatMinutesLate($minutesLate);
            }
        }
        // Ensure keterangan doesn't mention "terlambat" if status isn't "terlambat" 
        else if (stripos($keterangan, 'terlambat') !== false) {
            $keterangan = $request->status === 'hadir' ? 'Hadir tepat waktu' : 'Tidak hadir';
        }

        $absensi->update([
            'status' => $request->status,
            'keterangan' => $keterangan,
            'minutes_late' => $minutesLate,
        ]);
        
        return redirect()->back()->with('success', 'Status kehadiran berhasil diperbarui');
    }

    public function processQr(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'qr_data' => 'required|string',
            'jadwal_id' => 'required|exists:jadwal_mengajar,id'
        ]);
          try {
            $qrData = $request->qr_data;
            $jadwalId = $request->jadwal_id;
            $tanggal = now()->toDateString();
            
            // Create a unique lock key for this QR scan request
            $lockKey = "qr_scan_" . md5($qrData . $jadwalId . $tanggal);
            $lockFile = storage_path("app/locks/{$lockKey}.lock");
            
            // Check if request is already being processed
            if (file_exists($lockFile)) {
                $lockTime = filemtime($lockFile);
                if (time() - $lockTime < 10) { // 10 seconds lock timeout
                    return response()->json([
                        'success' => false, 
                        'message' => 'QR code sedang diproses. Silakan tunggu sebentar.'
                    ], 429); // 429 Too Many Requests
                } else {
                    // Lock file is stale, remove it
                    @unlink($lockFile);
                }
            }

            // Create lock file
            if (!is_dir(storage_path('app/locks'))) {
                mkdir(storage_path('app/locks'), 0755, true);
            }            file_put_contents($lockFile, time());

            // Find student
            $siswa = null;
            if (str_starts_with($qrData, '{')) {
                $jsonData = json_decode($qrData, true);
                if ($jsonData && isset($jsonData['nisn'])) {
                    $siswa = Siswa::where('nisn', $jsonData['nisn'])->first();
                }
            }
            if (!$siswa) {
                $siswa = Siswa::where('qr_token', $qrData)->orWhere('nisn', $qrData)->first();
            }

            if (!$siswa) {
                return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan']);
            }

            $jadwal = JadwalMengajar::find($jadwalId);
            if (!$jadwal || $siswa->kelas_id != $jadwal->kelas_id) {
                return response()->json(['success' => false, 'message' => 'Siswa tidak terdaftar di kelas ini']);
            }

            // Check if attendance already exists for this student, schedule, and date
            $existingAbsensi = Absensi::where([
                'siswa_id' => $siswa->id,
                'jadwal_id' => $jadwalId,
                'tanggal' => $tanggal
            ])->first();

            if ($existingAbsensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehadiran sudah dicatat sebelumnya untuk siswa ini pada jadwal dan tanggal yang sama',
                    'existing_record' => [
                        'status' => $existingAbsensi->status,
                        'keterangan' => $existingAbsensi->keterangan,
                        'waktu_dicatat' => $existingAbsensi->created_at->format('H:i:s')
                    ]
                ], 409); // 409 Conflict
            }            // Determine lateness with tolerance system (same as manual attendance)
            $status = 'hadir';
            $minutesLate = 0;
            $now = now();
            $jamMulai = Carbon::parse($now->format('Y-m-d') . ' ' . $jadwal->jam_mulai);
            
            // Check if late tolerance system is enabled
            $toleranceSystemEnabled = (bool) Setting::getSetting('enable_late_tolerance_system', true);
            
            if ($now->gt($jamMulai)) {
                $actualMinutesLate = $jamMulai->diffInMinutes($now);
                
                // Sanity check: if minutes late is more than 24 hours (1440 minutes), 
                // something is wrong with the calculation - default to 0
                if ($actualMinutesLate > 1440) {
                    $minutesLate = 0;
                } else {
                    $actualMinutesLate = (int) $actualMinutesLate;
                    
                    if ($toleranceSystemEnabled) {
                        // Get late tolerance setting (default 5 minutes)
                        $lateToleranceMinutes = (int) Setting::getSetting('late_tolerance_minutes', 5);
                        
                        if ($actualMinutesLate <= $lateToleranceMinutes) {
                            // Within tolerance - mark as on time
                            $status = 'hadir';
                            $minutesLate = 0;                        
                        } else {
                            // Exceed tolerance - mark as late with excess minutes
                            $status = 'terlambat';
                            $minutesLate = $actualMinutesLate - $lateToleranceMinutes;
                        }
                    } else {
                        // Tolerance system disabled - record actual lateness but mark as present
                        $status = 'hadir';
                        $minutesLate = $actualMinutesLate;
                    }
                }
            }
            
            $keterangan = ($status === 'terlambat' || $minutesLate > 0) ? $this->formatMinutesLate($minutesLate) : 'Hadir tepat waktu';            // Create attendance record
            DB::beginTransaction();
            try {
                $absensi = Absensi::create([
                    'tanggal' => $tanggal,
                    'jadwal_id' => $jadwalId,
                    'guru_id' => $guru->id,
                    'siswa_id' => $siswa->id,
                    'status' => $status,
                    'minutes_late' => $minutesLate,
                    'keterangan' => $keterangan,
                ]);
                  
                DB::commit();
                
                // Send WhatsApp notification to parent (async, don't block response)
                try {
                    $this->attendanceNotificationService->sendAttendanceNotification($absensi);
                } catch (\Exception $e) {
                    // Log error but don't fail the attendance recording
                }
                
                return response()->json([
                    'success' => true,
                    'message' => ($status === 'terlambat' || $minutesLate > 0) 
                        ? "Kehadiran dicatat (" . $this->formatMinutesLate($minutesLate) . ")" 
                        : 'Kehadiran berhasil dicatat',
                    'siswa' => [
                        'nama' => $siswa->nama_lengkap,
                        'nisn' => $siswa->nisn,
                        'kelas' => $siswa->kelas->nama_kelas ?? '-',
                        'status' => $status,
                        'minutes_late' => $minutesLate
                    ]
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        } finally {
            // Clean up lock file
            if (isset($lockFile) && file_exists($lockFile)) {
                @unlink($lockFile);
            }
        }
    }    public function manualAttendance(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $request->validate([
            'nisn' => 'required|string',
            'jadwal_id' => 'required|exists:jadwal_mengajar,id'
        ]);
        
        try {
            $tanggal = now()->toDateString();
            $jadwalId = $request->jadwal_id;
            $nisn = $request->nisn;
            
            // Create a unique lock key for this manual attendance request
            $lockKey = "manual_attendance_" . md5($nisn . $jadwalId . $tanggal);
            $lockFile = storage_path("app/locks/{$lockKey}.lock");
            
            // Check if request is already being processed
            if (file_exists($lockFile)) {
                $lockTime = filemtime($lockFile);
                if (time() - $lockTime < 10) { // 10 seconds lock timeout
                    return response()->json([
                        'success' => false, 
                        'message' => 'Absensi sedang diproses. Silakan tunggu sebentar.'
                    ], 429); // 429 Too Many Requests
                } else {
                    // Lock file is stale, remove it
                    @unlink($lockFile);
                }
            }

            // Create lock file
            if (!is_dir(storage_path('app/locks'))) {
                mkdir(storage_path('app/locks'), 0755, true);
            }
            file_put_contents($lockFile, time());
            
            $siswa = Siswa::where('nisn', $nisn)->first();
            if (!$siswa) {
                return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan']);
            }

            $jadwal = JadwalMengajar::find($jadwalId);
            if (!$jadwal || $siswa->kelas_id != $jadwal->kelas_id) {
                return response()->json(['success' => false, 'message' => 'Siswa tidak terdaftar di kelas ini']);
            }

            // Check if attendance already exists for this student, schedule, and date
            $existingAbsensi = Absensi::where([
                'siswa_id' => $siswa->id,
                'jadwal_id' => $jadwalId,
                'tanggal' => $tanggal
            ])->first();            
            if ($existingAbsensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehadiran sudah dicatat sebelumnya untuk siswa ini pada jadwal dan tanggal yang sama',
                    'existing_record' => [
                        'status' => $existingAbsensi->status,
                        'keterangan' => $existingAbsensi->keterangan,
                        'waktu_dicatat' => $existingAbsensi->created_at->format('H:i:s')
                    ]
                ], 409); // 409 Conflict
            }            // Determine lateness with tolerance system
            $status = 'hadir';
            $minutesLate = 0;
            $now = now();
            $jamMulai = Carbon::parse($now->format('Y-m-d') . ' ' . $jadwal->jam_mulai);
            
            // Check if late tolerance system is enabled
            $toleranceSystemEnabled = (bool) Setting::getSetting('enable_late_tolerance_system', true);
            
            if ($now->gt($jamMulai)) {
                $actualMinutesLate = $jamMulai->diffInMinutes($now);
                
                // Sanity check: if minutes late is more than 24 hours (1440 minutes), 
                // something is wrong with the calculation - default to 0
                if ($actualMinutesLate > 1440) {
                    $minutesLate = 0;
                } else {
                    $actualMinutesLate = (int) $actualMinutesLate;
                    
                    if ($toleranceSystemEnabled) {
                        // Get late tolerance setting (default 5 minutes)
                        $lateToleranceMinutes = (int) Setting::getSetting('late_tolerance_minutes', 5);
                        
                        if ($actualMinutesLate <= $lateToleranceMinutes) {
                            // Within tolerance - mark as on time
                            $status = 'hadir';
                            $minutesLate = 0;                        } else {
                            // Exceed tolerance - mark as late with excess minutes
                            $status = 'terlambat';
                            $minutesLate = $actualMinutesLate - $lateToleranceMinutes;
                        }
                    } else {
                        // Tolerance system disabled - record actual lateness but mark as present
                        $status = 'hadir';
                        $minutesLate = $actualMinutesLate;
                    }
                }
            }
            
            $keterangan = ($status === 'terlambat' || $minutesLate > 0) ? $this->formatMinutesLate($minutesLate) : 'Hadir tepat waktu';
              DB::beginTransaction();
            try {
                $absensi = Absensi::create([
                    'tanggal' => $tanggal,
                    'jadwal_id' => $jadwalId,
                    'guru_id' => $guru->id,                    
                    'siswa_id' => $siswa->id,
                    'status' => $status,
                    'minutes_late' => $minutesLate,
                    'keterangan' => $keterangan,
                ]);
                  
                DB::commit();
                
                // Send WhatsApp notification to parent (async, don't block response)
                try {
                    $this->attendanceNotificationService->sendAttendanceNotification($absensi);
                } catch (\Exception $e) {
                    // Log error but don't fail the attendance recording
                }
                
                return response()->json([
                    'success' => true,
                    'message' => ($status === 'terlambat' || $minutesLate > 0) 
                        ? "Kehadiran dicatat (" . $this->formatMinutesLate($minutesLate) . ")" 
                        : 'Kehadiran berhasil dicatat',
                    'siswa' => $siswa->only(['nama_lengkap', 'nisn']),
                    'kelas' => $siswa->kelas->nama_kelas ?? '-',
                    'status' => $status,
                    'minutes_late' => $minutesLate
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        } finally {
            // Clean up lock file
            if (isset($lockFile) && file_exists($lockFile)) {
                @unlink($lockFile);
            }
        }
    }

    public function completeAttendance(Request $request, $absensiId)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('guru.login')->with('error', 'Anda harus login terlebih dahulu');
        }
        
        $request->validate([
            'materi' => 'required|string|max:255',
            'kegiatan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);
        
        try {
            $absensi = Absensi::findOrFail($absensiId);
            $jadwalId = $absensi->jadwal_id;
            $tanggal = $absensi->tanggal;
            
            // Create journal
            JurnalMengajar::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'jadwal_id' => $jadwalId,
                    'guru_id' => $guru->id,
                ],
                [
                    'materi' => $request->materi,
                    'kegiatan' => $request->kegiatan,
                    'catatan' => $request->catatan,
                ]
            );
            
            // Mark attendance as completed
            Absensi::where('tanggal', $tanggal)
                ->where('jadwal_id', $jadwalId)
                ->where('guru_id', $guru->id)
                ->update(['is_completed' => true]);
            
            // Mark absent students as 'alpha'
            $jadwal = JadwalMengajar::with('kelas.siswa')->find($jadwalId);
            if ($jadwal && $jadwal->kelas) {
                $presentSiswaIds = Absensi::where('tanggal', $tanggal)
                    ->where('jadwal_id', $jadwalId)
                    ->pluck('siswa_id')
                    ->toArray();
                  $jadwal->kelas->siswa()->whereNotIn('id', $presentSiswaIds)->each(function ($siswa) use ($tanggal, $jadwalId, $guru) {
                    $absensi = Absensi::create([
                        'tanggal' => $tanggal,
                        'jadwal_id' => $jadwalId,
                        'guru_id' => $guru->id,
                        'siswa_id' => $siswa->id,
                        'status' => 'alpha',
                        'keterangan' => 'Tidak hadir',
                        'is_completed' => true
                    ]);
                    
                    // Send WhatsApp notification to parent for absent student
                    try {
                        $this->attendanceNotificationService->sendAttendanceNotification($absensi);
                    } catch (\Exception $e) {
                        // Log error but don't fail the completion process
                    }
                });
            }
            
            return redirect()->route('guru.absensi.index')->with('success', 'Absensi berhasil diselesaikan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function riwayat()
    {
        $guru = Auth::guard('guru')->user();
        $riwayatAbsensi = Absensi::where('guru_id', $guru->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
            
        return view('guru.absensi.riwayat', compact('riwayatAbsensi'));
    }

    public function detail($jadwal, $tanggal)
    {
        $jadwalMengajar = JadwalMengajar::with(['kelas', 'pelajaran'])->findOrFail($jadwal);
        
        $absensi = Absensi::where('jadwal_id', $jadwal)
            ->whereDate('tanggal', $tanggal)
            ->with('siswa')
            ->get();
            
        return view('guru.absensi.detail', compact('jadwalMengajar', 'absensi', 'tanggal'));
    }

    /**
     * Format minutes to a more readable hours and minutes format.
     *
     * @param int $minutes
     * @return string
     */
    private function formatMinutesLate($minutes)
    {
        $minutes = abs($minutes);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($hours > 0 && $remainingMinutes > 0) {
            return "Terlambat " . $hours . " jam " . $remainingMinutes . " menit";
        } elseif ($hours > 0) {
            return "Terlambat " . $hours . " jam";
        } else {
            return "Terlambat " . $remainingMinutes . " menit";
        }
    }
}