<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalMengajar;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Pelajaran;

class JadwalMengajarController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalMengajar::with(['guru', 'kelas', 'pelajaran']);
        
        // Apply filters if they exist
        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }
        
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        
        $jadwal = $query->get();
        return view('admin.jadwal.index', compact('jadwal'));
    }
    
    public function create()
    {
        $guru = Guru::all();
        $kelas = Kelas::all();
        $pelajaran = Pelajaran::all();
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return view('admin.jadwal.create', compact('guru', 'kelas', 'pelajaran', 'hari'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'kelas_id' => 'required|exists:kelas,id',
            'pelajaran_id' => 'required|exists:pelajaran,id',
            'hari' => 'required|integer|between:1,7',
            'jam_ke' => 'nullable|string',
            'jam_ke_awal' => 'required|integer|min:1',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'durasi' => 'required|integer|min:1|max:6',
            'menit_per_jp' => 'required|integer|min:30|max:60',
        ]);
        
        try {
            // Get periods array from comma-separated string
            $periods = array_map('intval', explode(',', $request->jam_ke));
            
            // Use first period as the primary jam_ke (for maintaining compatibility)
            $primaryPeriod = !empty($periods) ? $periods[0] : null;
            
            JadwalMengajar::create([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'pelajaran_id' => $request->pelajaran_id,
                'hari' => $request->hari,
                'jam_ke' => $primaryPeriod,
                'jam_ke_list' => $periods,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ]);
            
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal mengajar berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage())->withInput();
        }
    }
    
    public function edit($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $guru = Guru::all();
        $kelas = Kelas::all();
        $pelajaran = Pelajaran::all();
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return view('admin.jadwal.edit', compact('jadwal', 'guru', 'kelas', 'pelajaran', 'hari'));
    }
    
    public function update(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        
        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'kelas_id' => 'required|exists:kelas,id',
            'pelajaran_id' => 'required|exists:pelajaran,id',
            'hari' => 'required|integer|between:1,7',
            'jam_ke' => 'nullable|string',
            'jam_ke_awal' => 'required|integer|min:1',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'durasi' => 'required|integer|min:1|max:6',
            'menit_per_jp' => 'required|integer|min:30|max:60',
        ]);
        
        try {
            // Get periods array from comma-separated string
            $periods = array_map('intval', explode(',', $request->jam_ke));
            
            // Use first period as the primary jam_ke (for maintaining compatibility)
            $primaryPeriod = !empty($periods) ? $periods[0] : null;
            
            $jadwal->update([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'pelajaran_id' => $request->pelajaran_id,
                'hari' => $request->hari,
                'jam_ke' => $primaryPeriod,
                'jam_ke_list' => $periods,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ]);
            
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal mengajar berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update jadwal: ' . $e->getMessage())->withInput();
        }
    }
    
    public function destroy($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $jadwal->delete();
        
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal mengajar berhasil dihapus');
    }
    
    public function showByGuru($id)
    {
        $guru = Guru::findOrFail($id);
        $jadwal = JadwalMengajar::with(['kelas', 'pelajaran'])
                                ->where('guru_id', $id)
                                ->orderBy('hari')
                                ->orderBy('jam_mulai')
                                ->get();
                                
        return view('admin.jadwal.guru', compact('jadwal', 'guru'));
    }
    
    public function showByKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $jadwal = JadwalMengajar::with(['guru', 'pelajaran'])
                                ->where('kelas_id', $id)
                                ->orderBy('hari')
                                ->orderBy('jam_mulai')
                                ->get();
                                
        return view('admin.jadwal.kelas', compact('jadwal', 'kelas'));
    }
}
