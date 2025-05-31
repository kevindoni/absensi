<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\JadwalMengajar;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresensiController extends Controller
{
    /**
     * Menampilkan halaman presensi guru
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $today = Carbon::now()->toDateString();
        
        // Ambil jadwal mengajar hari ini
        $jadwalHariIni = JadwalMengajar::where('guru_id', $guru->id)
                ->where('hari', Carbon::now()->dayOfWeekIso)
                ->orderBy('jam_mulai')
                ->with(['kelas', 'pelajaran'])
                ->get();
                
        // Ambil presensi hari ini
        $presensiHariIni = Presensi::where('guru_id', $guru->id)
                ->whereDate('tanggal', $today)
                ->get()
                ->keyBy('jadwal_id');
                
        // Ambil riwayat presensi 7 hari terakhir
        $riwayatPresensi = Presensi::where('guru_id', $guru->id)
                ->whereDate('tanggal', '>=', Carbon::now()->subDays(7))
                ->orderBy('tanggal', 'desc')
                ->with(['jadwal.kelas', 'jadwal.pelajaran'])
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
        
        return view('guru.presensi.index', compact('jadwalHariIni', 'presensiHariIni', 'riwayatPresensi', 'today'));
    }

    /**
     * Melakukan check-in guru
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_mengajar,id'
        ]);
        
        $guru = Auth::guard('guru')->user();
        $jadwal = JadwalMengajar::findOrFail($request->jadwal_id);
        $now = Carbon::now();
        
        // Cek apakah jadwal ini milik guru yang sedang login
        if ($jadwal->guru_id != $guru->id) {
            return redirect()->route('guru.presensi.index')->with('error', 'Jadwal ini bukan milik Anda');
        }
        
        // Cek apakah sudah melakukan check-in
        $presensi = Presensi::where('guru_id', $guru->id)
                ->where('jadwal_id', $jadwal->id)
                ->whereDate('tanggal', $now->toDateString())
                ->first();
                
        if ($presensi) {
            return redirect()->route('guru.presensi.index')->with('error', 'Anda sudah melakukan check-in untuk kelas ini');
        }        // Cek apakah waktunya sesuai dengan jadwal        
        $jamMulai = Carbon::parse($now->format('Y-m-d') . ' ' . $jadwal->jam_mulai);
        // Ambil nilai toleransi dari database settings untuk guru
        $toleransiLogin = (int)Setting::getSetting('late_tolerance_minutes', 5);
        $maxToleranceTeacher = (int)Setting::getSetting('teacher_max_late_minutes', 5);
        $enableToleranceSystem = (bool)Setting::getSetting('enable_late_tolerance_system', true);
        $batasCheckIn = $jamMulai->copy()->addMinutes($maxToleranceTeacher); // Batas maksimal terlambat untuk guru
        $batasToleransi = $jamMulai->copy()->addMinutes($toleransiLogin); // Batas toleransi terlambat
        $batasCheckInAwal = $jamMulai->copy()->subMinutes($toleransiLogin); // Batas datang lebih awal
        
        // Buat presensi baru
        $presensi = new Presensi();
        $presensi->guru_id = $guru->id;
        $presensi->jadwal_id = $jadwal->id;
        $presensi->tanggal = $now->toDateString();
        $presensi->waktu_masuk = $now;
        
        // Cek waktu kedatangan
        if ($now->lt($batasCheckInAwal)) {
            // Terlalu awal (lebih dari toleransi menit sebelum jadwal)
            return redirect()->route('guru.presensi.index')
                ->with('error', 'Anda datang terlalu awal. Silakan tunggu maksimal ' . $toleransiLogin . ' menit sebelum jadwal.');
        }        if ($now->gt($jamMulai)) {
            $menit_terlambat = $now->diffInMinutes($jamMulai);
              if (!$enableToleranceSystem) {
                // Sistem toleransi dinonaktifkan - semua keterlambatan dicatat sebagai hadir
                $presensi->status = 'hadir';
                $presensi->keterangan = $menit_terlambat > 0 ? 'Terlambat ' . $this->formatDuration($menit_terlambat) : 'Hadir tepat waktu';
            } else {
                // Sistem toleransi aktif - gunakan logic normal
                if ($now->gt($batasCheckIn)) {
                    // Terlambat lebih dari batas maksimum
                    $presensi->status = 'terlambat';
                    $presensi->keterangan = 'Terlambat ' . $this->formatDuration($menit_terlambat) . 
                        '. Batas maksimum (' . $maxToleranceTeacher . ' menit) keterlambatan';
                } else if ($now->gt($batasToleransi)) {
                    // Terlambat tapi masih dalam batas maksimum
                    $presensi->status = 'terlambat';
                    $presensi->keterangan = 'Terlambat ' . $this->formatDuration($menit_terlambat);
                } else {
                    // Masih dalam toleransi
                    $presensi->status = 'hadir';
                    $presensi->keterangan = 'Hadir dalam toleransi waktu';
                }
            }
        } else {
            // Hadir lebih awal (dalam rentang toleransi)
            $menit_lebih_awal = $jamMulai->diffInMinutes($now);
            $presensi->status = 'hadir';
            $presensi->keterangan = 'Hadir ' . $this->formatDuration($menit_lebih_awal) . ' lebih awal';
        }

        $presensi->save();
        
        if ($presensi->status === 'terlambat') {
            return redirect()->route('guru.presensi.index')
                ->with('warning', $presensi->keterangan);
        }

        return redirect()->route('guru.presensi.index')
            ->with('success', 'Check-in berhasil: ' . $presensi->keterangan);
    }

    /**
     * Melakukan check-out guru
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'presensi_id' => 'required|exists:presensi,id'
        ]);
        
        $guru = Auth::guard('guru')->user();
        $presensi = Presensi::findOrFail($request->presensi_id);
        $now = Carbon::now();
        
        // Cek apakah presensi ini milik guru yang sedang login
        if ($presensi->guru_id != $guru->id) {
            return redirect()->route('guru.presensi.index')->with('error', 'Data presensi ini bukan milik Anda');
        }
        
        // Cek apakah sudah ada waktu keluar
        if ($presensi->waktu_keluar) {
            return redirect()->route('guru.presensi.index')->with('error', 'Anda sudah melakukan check-out');
        }
        
        // Get jadwal information for duration calculation
        $jadwal = JadwalMengajar::find($presensi->jadwal_id);
        $jamSelesai = null;
        if ($jadwal) {
            $jamSelesai = Carbon::parse($now->format('Y-m-d') . ' ' . $jadwal->jam_selesai);
        }
        
        $presensi->waktu_keluar = $now;
        
        // If checking out before scheduled end time, add a note
        if ($jamSelesai && $now->lt($jamSelesai)) {
            $menit_awal = $now->diffInMinutes($jamSelesai);
            $presensi->keterangan = ($presensi->keterangan ? $presensi->keterangan . '. ' : '') . 
            'Keluar ' . $this->formatDuration($menit_awal) . ' lebih awal';
        }
        
        // Calculate duration of class
        $duration = $presensi->waktu_masuk->diffInMinutes($now);
        if ($duration > 0) {
            $presensi->keterangan = ($presensi->keterangan ? $presensi->keterangan . '. ' : '') . 
            'Durasi mengajar: ' . $this->formatDuration($duration);
        }
        
        $presensi->save();
        
        return redirect()->route('guru.presensi.index')->with('success', 'Check-out berhasil');
    }

    /**
     * Menampilkan laporan presensi guru
     */
    public function report()
    {
        $guru = Auth::guard('guru')->user();
        
        // Default: bulan ini
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        
        if (request()->has('bulan') && request()->has('tahun')) {
            $bulan = request()->bulan;
            $tahun = request()->tahun;
        }
        
        $presensi = Presensi::where('guru_id', $guru->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->with(['jadwal.kelas', 'jadwal.pelajaran'])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });
            
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return view('guru.presensi.report', compact('presensi', 'bulan', 'tahun', 'namaBulan'));
    }

    /**
     * Format duration in minutes to a more readable format (e.g. "2 jam 15 menit" or "30 menit")
     *
     * @param int $minutes
     * @return string
     */
    private function formatDuration($minutes)
    {
        // Ensure we're working with a positive integer
        $minutes = abs((int)$minutes);
        
        if ($minutes < 60) {
            return $minutes . ' menit';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . ' jam';
        }
        
        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }
}
