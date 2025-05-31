<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\Izin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Display the guru dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function guruDashboard()
    {
        return view('guru.dashboard');
    }

    /**
     * Display the siswa dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function siswaDashboard()
    {
        $siswa = auth()->user();
        
        // Get attendance statistics
        $totalHadir = Absensi::where('siswa_id', $siswa->id)
            ->where('status', 'hadir')
            ->count();
            
        $totalAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereIn('status', ['alpha', 'sakit', 'izin'])
            ->count();
            
        $totalKeseluruhan = $totalHadir + $totalAbsen;
        $persentaseKehadiran = $totalKeseluruhan > 0 ? 
            round(($totalHadir / $totalKeseluruhan) * 100, 1) : 0;
            
        $izinPending = Izin::where('siswa_id', $siswa->id)
            ->where('status', 'pending')
            ->count();
            
        // Get attendance history for chart
        $riwayatKehadiran = Absensi::where('siswa_id', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('d/m');
            });
            
        // Get attendance distribution for pie chart
        $distribusiStatus = Absensi::where('siswa_id', $siswa->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('siswa.dashboard', compact(
            'totalHadir',
            'totalAbsen',
            'persentaseKehadiran',
            'izinPending',
            'riwayatKehadiran',
            'distribusiStatus'
        ));
    }

    /**
     * Display the orangtua dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function orangtuaDashboard()
    {
        $orangtua = Auth::guard('orangtua')->user();
        
        // Get unread notifications
        $notifications = $orangtua->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('orangtua.dashboard', compact('notifications'));
    }

    /**
     * Display the admin profile page.
     *
     * @return \Illuminate\View\View
     */
    public function adminProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profil', compact('admin'));
    }

    /**
     * Display the guru profile page.
     *
     * @return \Illuminate\View\View
     */
    public function guruProfile()
    {
        $guru = Auth::guard('guru')->user();
        return view('guru.profil', compact('guru'));
    }

    /**
     * Display the siswa profile page.
     *
     * @return \Illuminate\View\View
     */
    public function siswaProfile()
    {
        return view('siswa.profil.index');
    }

    /**
     * Display the orangtua profile page.
     *
     * @return \Illuminate\View\View
     */
    public function orangtuaProfile()
    {
        $orangtua = Auth::guard('orangtua')->user();
        $orangtua->load(['siswa' => function($query) {
            $query->with('kelas');
        }]);
        return view('orangtua.profil.index', compact('orangtua'));
    }

    /**
     * Display admin reports page.
     *
     * @return \Illuminate\View\View
     */
    public function adminLaporan()
    {
        return view('admin.laporan.index');
    }
}
