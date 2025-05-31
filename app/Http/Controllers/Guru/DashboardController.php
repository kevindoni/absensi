<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalMengajar;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Presensi; // Add this line
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the guru dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $now = Carbon::now();
        $today = Carbon::now()->toDateString();
        $dayOfWeek = Carbon::now()->dayOfWeekIso;
        
        // Get unique classes taught by the teacher
        $kelasYangDiajar = JadwalMengajar::where('guru_id', $guru->id)
                      ->where('hari', $dayOfWeek)
                      ->with(['kelas', 'pelajaran'])
                      ->orderBy('jam_mulai')
                      ->get();
        
        // Count the number of unique classes
        $kelasYangDiajarCount = $kelasYangDiajar->pluck('kelas_id')->unique()->count();
        
        // Get all jadwal
        $allJadwal = JadwalMengajar::where('guru_id', $guru->id)
                    ->with(['kelas', 'pelajaran'])
                    ->orderBy('hari')
                    ->orderBy('jam_mulai')
                    ->get();
        
        // Get unique student IDs from all the classes taught by this teacher
        $kelasList = JadwalMengajar::where('guru_id', $guru->id)
                   ->pluck('kelas_id')
                   ->unique();
        
        // Count total students in these classes
        $totalSiswa = Siswa::whereIn('kelas_id', $kelasList)->count();
                    
        // Get recent attendance records - adjusted to work without jadwal_id
        $recentAbsensi = Absensi::where('guru_id', $guru->id)
                        ->select('tanggal', DB::raw('COUNT(*) as total_siswa'))
                        ->groupBy('tanggal')
                        ->orderBy('tanggal', 'desc')
                        ->limit(5)
                        ->get();
        
        // Calculate today's attendance percentages and counts
        $todayAttendance = Absensi::where('guru_id', $guru->id)
                          ->whereDate('tanggal', $today)
                          ->get();
        
        $totalHadir = $todayAttendance->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalIzin = $todayAttendance->where('status', 'izin')->count();
        $totalSakit = $todayAttendance->where('status', 'sakit')->count();
        $totalAlpha = $todayAttendance->where('status', 'alpha')->count();
        
        // Calculate percentage
        $totalAttendanceToday = $totalHadir + $totalIzin + $totalSakit + $totalAlpha;
        $persentaseKehadiran = $totalAttendanceToday > 0 ? round(($totalHadir / $totalAttendanceToday) * 100) : 0;
                        
        // Get attendance statistics
        $statistics = [
            'today' => Absensi::where('guru_id', $guru->id)
                      ->whereDate('tanggal', Carbon::today())
                      ->count(),
            'week' => Absensi::where('guru_id', $guru->id)
                     ->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                     ->count(),
            'month' => Absensi::where('guru_id', $guru->id)
                      ->whereMonth('tanggal', Carbon::now()->month)
                      ->whereYear('tanggal', Carbon::now()->year)
                      ->count(),
            'total' => Absensi::where('guru_id', $guru->id)->count(),
        ];
        
        // Get statistics by status
        $statusStats = Absensi::where('guru_id', $guru->id)
                     ->select('status', DB::raw('COUNT(*) as count'))
                     ->groupBy('status')
                     ->get()
                     ->pluck('count', 'status')
                     ->toArray();
        
        // Prepare chart data for last 7 days
        $last7Days = [];
        $attendanceData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = $date->format('d/m');
            
            // Calculate attendance percentage for this day
            $dayAttendance = Absensi::where('guru_id', $guru->id)
                            ->whereDate('tanggal', $date->format('Y-m-d'))
                            ->get();
            
            $dayHadir = $dayAttendance->whereIn('status', ['hadir', 'terlambat'])->count();
            $dayTotal = $dayAttendance->count();
            
            // Avoid division by zero
            $attendanceData[] = $dayTotal > 0 ? round(($dayHadir / $dayTotal) * 100) : 0;
        }
        
        // Ambil jadwal mengajar hari ini
        $jadwalHariIni = JadwalMengajar::where('guru_id', $guru->id)
            ->where('hari', $now->dayOfWeekIso)
            ->with(['kelas', 'pelajaran']) // Removed jurusan from eager loading
            ->orderBy('jam_mulai')
            ->get();
            
        // Ambil presensi dan absensi hari ini
        $presensiHariIni = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $now->toDateString())
            ->get()
            ->keyBy('jadwal_id');
            
        $absensiHariIni = Absensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $now->toDateString())
            ->distinct('jadwal_id')
            ->get(['jadwal_id', 'is_completed'])
            ->keyBy('jadwal_id');

        return view('guru.dashboard', compact(
            'guru', 
            'kelasYangDiajar',
            'kelasYangDiajarCount',
            'allJadwal', 
            'recentAbsensi', 
            'statistics', 
            'statusStats',
            'totalSiswa',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpha',
            'persentaseKehadiran',
            'last7Days',
            'attendanceData',
            'jadwalHariIni',
            'presensiHariIni',
            'absensiHariIni',
            'now'
        ));
    }

    /**
     * Display the guru profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function profil()
    {
        $guru = Auth::guard('guru')->user();
        
        // Get statistics
        $stats = [
            'kelas' => JadwalMengajar::where('guru_id', $guru->id)
                      ->distinct('kelas_id')
                      ->count('kelas_id'),
                      
            'mapel' => JadwalMengajar::where('guru_id', $guru->id)
                      ->distinct('pelajaran_id')
                      ->count('pelajaran_id'),
                      
            'absensi' => Absensi::where('guru_id', $guru->id)->count(),
            
            'jadwal' => JadwalMengajar::where('guru_id', $guru->id)->count()
        ];
        
        return view('guru.profil', compact('guru', 'stats'));
    }
}
