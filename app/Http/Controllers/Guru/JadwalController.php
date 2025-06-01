<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalController extends Controller
{
    /**
     * Display the main schedule page
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        
        // Get current week start and end
        $currentDate = Carbon::now();
        $startOfWeek = $currentDate->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
        
        // Get view type from request (default: weekly)
        $viewType = $request->get('view', 'weekly');
        
        if ($viewType === 'monthly') {
            $startDate = $currentDate->copy()->startOfMonth();
            $endDate = $currentDate->copy()->endOfMonth();
        } else {
            $startDate = $startOfWeek;
            $endDate = $endOfWeek;
        }
        
        // Get all schedules for the teacher
        $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
            ->with(['kelas', 'pelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
        
        // Group schedules by day for easy display
        $jadwalPerHari = $jadwalMengajar->groupBy('hari');
          // Get schedule statistics
        $totalJadwal = $jadwalMengajar->count();
        $totalKelas = $jadwalMengajar->pluck('kelas_id')->unique()->count();
        $totalMapel = $jadwalMengajar->pluck('pelajaran_id')->unique()->count();
        
        // Calculate weekly hours
        $weeklyHours = $jadwalMengajar->sum(function($jadwal) {
            $jamMulai = Carbon::parse($jadwal->jam_mulai);
            $jamSelesai = Carbon::parse($jadwal->jam_selesai);
            return $jamMulai->diffInHours($jamSelesai);
        });
        
        // Get active days count
        $activeDays = $jadwalMengajar->pluck('hari')->unique()->count();
        
        // Prepare statistics array for view
        $statistics = [
            'total_classes' => $totalKelas,
            'total_subjects' => $totalMapel,
            'weekly_hours' => $weeklyHours,
            'active_days' => $activeDays
        ];
        
        // Generate subject colors for calendar
        $subjectColors = [];
        $colors = ['#3788d8', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de', '#292b2c', '#337ab7', '#449d44', '#ec971f', '#c9302c'];
        $subjects = $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique();
        foreach ($subjects as $index => $subject) {
            $subjectColors[$subject] = $colors[$index % count($colors)];
        }
        
        // Get today's schedule
        $today = Carbon::now()->dayOfWeekIso; // 1=Monday, 7=Sunday
        $todaySchedule = $jadwalMengajar->filter(function($jadwal) use ($today) {
            return $jadwal->hari == $today;
        })->sortBy('jam_mulai');
        
        // Days mapping
        $hariMapping = [
            1 => 'Senin',
            2 => 'Selasa', 
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return view('guru.jadwal.index', compact(
            'jadwalMengajar',
            'jadwalPerHari',
            'statistics',
            'subjectColors',
            'todaySchedule',
            'hariMapping',
            'viewType',
            'currentDate'
        ));
    }
    
    /**
     * Get schedule data for calendar (AJAX)
     */
    public function getScheduleData(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        
        $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
            ->with(['kelas', 'pelajaran'])
            ->get();
        
        $events = [];
        
        foreach ($jadwalMengajar as $jadwal) {
            // Convert day number to day of week for current week
            $dayOfWeek = $jadwal->hari; // 1=Monday, 2=Tuesday, etc.
            
            // Get current week's date for this day
            $currentWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $eventDate = $currentWeek->copy()->addDays($dayOfWeek - 1);
            
            $events[] = [
                'id' => $jadwal->id,
                'title' => $jadwal->pelajaran->nama_pelajaran . ' - ' . $jadwal->kelas->nama_kelas,
                'start' => $eventDate->format('Y-m-d') . 'T' . $jadwal->jam_mulai,
                'end' => $eventDate->format('Y-m-d') . 'T' . $jadwal->jam_selesai,
                'backgroundColor' => $this->getRandomColor($jadwal->pelajaran_id),
                'borderColor' => $this->getRandomColor($jadwal->pelajaran_id),
                'extendedProps' => [
                    'kelas' => $jadwal->kelas->nama_kelas,
                    'pelajaran' => $jadwal->pelajaran->nama_pelajaran,
                    'jam_ke' => $jadwal->jam_ke,
                    'hari' => $this->getDayName($jadwal->hari)
                ]
            ];
        }
        
        return response()->json($events);
    }
    
    /**
     * Preview PDF before download
     */
    public function previewPdf()
    {
        try {
            $guru = Auth::guard('guru')->user();
            
            $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
                ->with(['kelas', 'pelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
            
            if ($jadwalMengajar->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal mengajar untuk diekspor.');
            }
            
            $jadwalPerHari = $jadwalMengajar->groupBy('hari');
            
            $hariMapping = [
                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
            ];
            
            $statistics = [
                'total_classes' => $jadwalMengajar->pluck('kelas_id')->unique()->count(),
                'total_subjects' => $jadwalMengajar->pluck('pelajaran_id')->unique()->count(),
                'total_schedules' => $jadwalMengajar->count(),
                'weekly_hours' => $jadwalMengajar->sum(function($jadwal) {
                    $start = Carbon::parse($jadwal->jam_mulai);
                    $end = Carbon::parse($jadwal->jam_selesai);
                    return $start->diffInHours($end);
                }),
                'active_days' => $jadwalMengajar->pluck('hari')->unique()->count(),
                'subjects_list' => $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique()->sort()->values(),
                'classes_list' => $jadwalMengajar->pluck('kelas.nama_kelas')->unique()->sort()->values()
            ];
            
            return view('guru.jadwal.pdf-preview', compact(
                'guru',
                'jadwalMengajar',
                'jadwalPerHari',
                'hariMapping',
                'statistics'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat preview: ' . $e->getMessage());
        }
    }

    /**
     * Export schedule to PDF with error handling
     */
    public function exportPdf()
    {
        try {
            $guru = Auth::guard('guru')->user();
            
            $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
                ->with(['kelas', 'pelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
            
            if ($jadwalMengajar->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal mengajar untuk diekspor.');
            }
            
            $jadwalPerHari = $jadwalMengajar->groupBy('hari');
            
            $hariMapping = [
                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
            ];
            
            // Calculate additional statistics
            $statistics = [
                'total_classes' => $jadwalMengajar->pluck('kelas_id')->unique()->count(),
                'total_subjects' => $jadwalMengajar->pluck('pelajaran_id')->unique()->count(),
                'total_schedules' => $jadwalMengajar->count(),
                'weekly_hours' => $jadwalMengajar->sum(function($jadwal) {
                    $start = Carbon::parse($jadwal->jam_mulai);
                    $end = Carbon::parse($jadwal->jam_selesai);
                    return $start->diffInHours($end);
                }),
                'active_days' => $jadwalMengajar->pluck('hari')->unique()->count(),
                'subjects_list' => $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique()->sort()->values(),
                'classes_list' => $jadwalMengajar->pluck('kelas.nama_kelas')->unique()->sort()->values()
            ];
            
            $pdf = Pdf::loadView('guru.jadwal.pdf', compact(
                'guru',
                'jadwalMengajar',
                'jadwalPerHari',
                'hariMapping',
                'statistics'
            ))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Times New Roman',
                'isRemoteEnabled' => false,
                'isPhpEnabled' => true,
                'chroot' => public_path(),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'tempDir' => storage_path('app/temp')
            ]);
            
            $filename = 'Jadwal_Mengajar_' . str_replace(' ', '_', $guru->nama_lengkap) . '_' . date('Y-m-d_His') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export compact schedule to PDF (grid layout) with error handling
     */
    public function exportCompactPdf()
    {
        try {
            $guru = Auth::guard('guru')->user();
            
            $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
                ->with(['kelas', 'pelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
            
            if ($jadwalMengajar->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal mengajar untuk diekspor.');
            }
            
            $jadwalPerHari = $jadwalMengajar->groupBy('hari');
            
            $hariMapping = [
                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
            ];
            
            $statistics = [
                'total_classes' => $jadwalMengajar->pluck('kelas_id')->unique()->count(),
                'total_subjects' => $jadwalMengajar->pluck('pelajaran_id')->unique()->count(),
                'total_schedules' => $jadwalMengajar->count(),
                'weekly_hours' => $jadwalMengajar->sum(function($jadwal) {
                    $start = Carbon::parse($jadwal->jam_mulai);
                    $end = Carbon::parse($jadwal->jam_selesai);
                    return $start->diffInHours($end);
                }),
                'active_days' => $jadwalMengajar->pluck('hari')->unique()->count(),
                'subjects_list' => $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique()->sort()->values(),
                'classes_list' => $jadwalMengajar->pluck('kelas.nama_kelas')->unique()->sort()->values()
            ];
            
            $pdf = Pdf::loadView('guru.jadwal.pdf-compact', compact(
                'guru',
                'jadwalMengajar',
                'jadwalPerHari',
                'hariMapping',
                'statistics'
            ))
            ->setPaper('A4', 'landscape')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => false,
                'isPhpEnabled' => true,
                'chroot' => public_path(),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'tempDir' => storage_path('app/temp')
            ]);
            
            $filename = 'Jadwal_Kompak_' . str_replace(' ', '_', $guru->nama_lengkap) . '_' . date('Y-m-d_His') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor PDF kompak: ' . $e->getMessage());
        }
    }
      /**
     * Get weekly schedule view
     */
    public function weekly(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        
        // Handle week parameter - use current week if not provided or invalid
        $week = $request->get('week');
        
        try {
            if ($week) {
                // Try to parse different week formats
                if (preg_match('/^(\d{4})-W(\d{1,2})$/', $week, $matches)) {
                    // Format: 2025-W23
                    $year = (int)$matches[1];
                    $weekNumber = (int)$matches[2];
                    $startOfWeek = Carbon::create($year, 1, 1)->setISODate($year, $weekNumber, 1);
                } elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $week)) {
                    // Format: 2025-06-01 (specific date)
                    $startOfWeek = Carbon::parse($week)->startOfWeek(Carbon::MONDAY);
                } else {
                    // Default to current week
                    $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                }
            } else {
                // Default to current week
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            }
        } catch (\Exception $e) {
            // Fallback to current week if parsing fails
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);
        
        $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
            ->with(['kelas', 'pelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
        
        $jadwalPerHari = $jadwalMengajar->groupBy('hari');
        
        $hariMapping = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis', 
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return view('guru.jadwal.weekly', compact(
            'jadwalPerHari',
            'hariMapping',
            'startOfWeek',
            'endOfWeek',
            'week'
        ));
    }
    
    /**
     * Get schedule details
     */
    public function show($id)
    {
        $guru = Auth::guard('guru')->user();
        
        $jadwal = JadwalMengajar::where('guru_id', $guru->id)
            ->where('id', $id)
            ->with(['kelas', 'pelajaran'])
            ->firstOrFail();
        
        return response()->json([
            'id' => $jadwal->id,
            'kelas' => $jadwal->kelas->nama_kelas,
            'pelajaran' => $jadwal->pelajaran->nama_pelajaran,
            'hari' => $this->getDayName($jadwal->hari),
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'jam_ke' => $jadwal->jam_ke
        ]);
    }
    
    /**
     * Get random color for calendar events
     */
    private function getRandomColor($seed)
    {
        $colors = [
            '#3788d8', '#5cb85c', '#f0ad4e', '#d9534f',
            '#5bc0de', '#292b2c', '#f7f7f7', '#337ab7',
            '#449d44', '#ec971f', '#c9302c', '#31b0d5'
        ];
        
        return $colors[$seed % count($colors)];
    }
    
    /**
     * Get day name from number
     */
    private function getDayName($dayNumber)
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return $days[$dayNumber] ?? '';
    }

    /**
     * Preview compact PDF before download
     */
    public function previewCompactPdf()
    {
        try {
            $guru = Auth::guard('guru')->user();
            
            $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
                ->with(['kelas', 'pelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
            
            if ($jadwalMengajar->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal mengajar untuk diekspor.');
            }
            
            $jadwalPerHari = $jadwalMengajar->groupBy('hari');
            
            $hariMapping = [
                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
            ];
            
            $statistics = [
                'total_classes' => $jadwalMengajar->pluck('kelas_id')->unique()->count(),
                'total_subjects' => $jadwalMengajar->pluck('pelajaran_id')->unique()->count(),
                'total_schedules' => $jadwalMengajar->count(),
                'weekly_hours' => $jadwalMengajar->sum(function($jadwal) {
                    $start = Carbon::parse($jadwal->jam_mulai);
                    $end = Carbon::parse($jadwal->jam_selesai);
                    return $start->diffInHours($end);
                }),
                'active_days' => $jadwalMengajar->pluck('hari')->unique()->count(),
                'subjects_list' => $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique()->sort()->values(),
                'classes_list' => $jadwalMengajar->pluck('kelas.nama_kelas')->unique()->sort()->values()
            ];
            
            return view('guru.jadwal.pdf-compact-preview', compact(
                'guru',
                'jadwalMengajar',
                'jadwalPerHari',
                'hariMapping',
                'statistics'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat preview kompak: ' . $e->getMessage());
        }
    }

    /**
     * Preview weekly PDF before download
     */
    public function previewWeeklyPdf(Request $request)
    {
        try {
            $guru = Auth::guard('guru')->user();
            
            // Handle week parameter - use current week if not provided or invalid
            $week = $request->get('week');
            
            try {
                if ($week) {
                    // Try to parse different week formats
                    if (preg_match('/^(\d{4})-W(\d{1,2})$/', $week, $matches)) {
                        // Format: 2025-W23
                        $year = (int)$matches[1];
                        $weekNumber = (int)$matches[2];
                        $startOfWeek = Carbon::create($year, 1, 1)->setISODate($year, $weekNumber, 1);
                    } elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $week)) {
                        // Format: 2025-06-01 (specific date)
                        $startOfWeek = Carbon::parse($week)->startOfWeek(Carbon::MONDAY);
                    } else {
                        // Default to current week
                        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                    }
                } else {
                    // Default to current week
                    $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                }
            } catch (\Exception $e) {
                // Fallback to current week if parsing fails
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            }
            
            $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);
            
            $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
                ->with(['kelas', 'pelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
            
            if ($jadwalMengajar->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal mengajar untuk diekspor.');
            }
            
            $jadwalPerHari = $jadwalMengajar->groupBy('hari');
            
            $hariMapping = [
                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
            ];
            
            $statistics = [
                'total_classes' => $jadwalMengajar->pluck('kelas_id')->unique()->count(),
                'total_subjects' => $jadwalMengajar->pluck('pelajaran_id')->unique()->count(),
                'total_schedules' => $jadwalMengajar->count(),
                'weekly_hours' => $jadwalMengajar->sum(function($jadwal) {
                    $start = Carbon::parse($jadwal->jam_mulai);
                    $end = Carbon::parse($jadwal->jam_selesai);
                    return $start->diffInHours($end);
                }),
                'active_days' => $jadwalMengajar->pluck('hari')->unique()->count(),
                'subjects_list' => $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique()->sort()->values(),
                'classes_list' => $jadwalMengajar->pluck('kelas.nama_kelas')->unique()->sort()->values()
            ];
            
            return view('guru.jadwal.pdf-weekly-preview', compact(
                'guru',
                'jadwalMengajar',
                'jadwalPerHari',
                'hariMapping',
                'statistics',
                'startOfWeek',
                'endOfWeek',
                'week'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat preview mingguan: ' . $e->getMessage());
        }
    }

    /**
     * Export weekly schedule to PDF
     */    public function exportWeeklyPdf(Request $request)
    {
        try {
            $guru = Auth::guard('guru')->user();
            
            // Handle week parameter - use current week if not provided or invalid
            $week = $request->get('week');
            
            try {
                if ($week) {
                    // Try to parse different week formats
                    if (preg_match('/^(\d{4})-W(\d{1,2})$/', $week, $matches)) {
                        // Format: 2025-W23
                        $year = (int)$matches[1];
                        $weekNumber = (int)$matches[2];
                        $startOfWeek = Carbon::create($year, 1, 1)->setISODate($year, $weekNumber, 1);
                    } elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $week)) {
                        // Format: 2025-06-01 (specific date)
                        $startOfWeek = Carbon::parse($week)->startOfWeek(Carbon::MONDAY);
                    } else {
                        // Default to current week
                        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                    }
                } else {
                    // Default to current week
                    $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                }
            } catch (\Exception $e) {
                // Fallback to current week if parsing fails
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            }
            
            $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);
            
            $jadwalMengajar = JadwalMengajar::where('guru_id', $guru->id)
                ->with(['kelas', 'pelajaran'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
            
            if ($jadwalMengajar->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal mengajar untuk diekspor.');
            }
            
            $jadwalPerHari = $jadwalMengajar->groupBy('hari');
            
            $hariMapping = [
                1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
            ];
            
            $statistics = [
                'total_classes' => $jadwalMengajar->pluck('kelas_id')->unique()->count(),
                'total_subjects' => $jadwalMengajar->pluck('pelajaran_id')->unique()->count(),
                'total_schedules' => $jadwalMengajar->count(),
                'weekly_hours' => $jadwalMengajar->sum(function($jadwal) {
                    $start = Carbon::parse($jadwal->jam_mulai);
                    $end = Carbon::parse($jadwal->jam_selesai);
                    return $start->diffInHours($end);
                }),
                'active_days' => $jadwalMengajar->pluck('hari')->unique()->count(),
                'subjects_list' => $jadwalMengajar->pluck('pelajaran.nama_pelajaran')->unique()->sort()->values(),
                'classes_list' => $jadwalMengajar->pluck('kelas.nama_kelas')->unique()->sort()->values()
            ];
            
            $pdf = Pdf::loadView('guru.jadwal.pdf-weekly', compact(
                'guru',
                'jadwalMengajar',
                'jadwalPerHari',
                'hariMapping',
                'statistics',
                'startOfWeek',
                'endOfWeek'
            ))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Times New Roman',
                'isRemoteEnabled' => false,
                'isPhpEnabled' => true,
                'chroot' => public_path(),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'tempDir' => storage_path('app/temp')
            ]);
            
            $weekLabel = $startOfWeek->format('d_M_Y') . '_to_' . $endOfWeek->format('d_M_Y');
            $filename = 'Jadwal_Mingguan_' . str_replace(' ', '_', $guru->nama_lengkap) . '_' . $weekLabel . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor PDF mingguan: ' . $e->getMessage());
        }
    }
}
