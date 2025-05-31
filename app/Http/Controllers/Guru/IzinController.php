<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\JadwalMengajar; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IzinController extends Controller
{
    /**
     * Menampilkan daftar izin siswa
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $izin = Absensi::whereIn('status', ['izin', 'sakit'])
                ->where('guru_id', $guru->id)
                ->with(['siswa', 'jadwal.pelajaran', 'jadwal.kelas'])
                ->latest()
                ->paginate(10);

        return view('guru.izin.index', compact('izin'));
    }

    /**
     * Menampilkan form untuk menambahkan izin baru
     */
    public function create()
    {
        $guru = Auth::guard('guru')->user();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        // Get classes taught by this teacher
        $kelasIds = JadwalMengajar::where('guru_id', $guru->id)
                    ->pluck('kelas_id')
                    ->unique();
                    
        // Get initial students list (from first class by default)
        $defaultKelasId = $kelasIds->first();
        $siswaList = $defaultKelasId ? 
                    Siswa::where('kelas_id', $defaultKelasId)
                        ->orderBy('nama_lengkap')
                        ->get() : 
                    collect();
        
        return view('guru.izin.create', compact('kelas', 'siswaList', 'kelasIds', 'defaultKelasId'));
    }

    /**
     * Menyimpan data izin baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:izin,sakit',
            'keterangan' => 'required|string',
            'bukti_surat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $guru = Auth::guard('guru')->user();
        $tanggal = $request->tanggal;
        
        // Cek apakah sudah ada absensi untuk siswa ini pada tanggal tersebut
        $existingAbsensi = Absensi::where('tanggal', $tanggal)
                            ->where('siswa_id', $request->siswa_id)
                            ->first();
        
        // Jika sudah ada, update status. Jika belum, buat baru.
        if ($existingAbsensi) {
            $existingAbsensi->status = $request->status;
            $existingAbsensi->keterangan = $request->keterangan;
            
            // Upload surat jika ada
            if ($request->hasFile('bukti_surat')) {
                // Hapus file lama jika ada
                if ($existingAbsensi->bukti_surat) {
                    Storage::delete('public/surat_izin/' . $existingAbsensi->bukti_surat);
                }
                
                $file = $request->file('bukti_surat');
                $filename = Str::slug($existingAbsensi->siswa->nama_lengkap) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/surat_izin', $filename);
                $existingAbsensi->bukti_surat = $filename;
            }
            
            $existingAbsensi->save();
            $message = 'Status absensi berhasil diperbarui menjadi ' . ucfirst($request->status);
        } else {
            $newAbsensi = new Absensi();
            $newAbsensi->tanggal = $tanggal;
            $newAbsensi->guru_id = $guru->id;
            $newAbsensi->siswa_id = $request->siswa_id;
            $newAbsensi->status = $request->status;
            $newAbsensi->keterangan = $request->keterangan;
            
            // Upload surat jika ada
            if ($request->hasFile('bukti_surat')) {
                $file = $request->file('bukti_surat');
                $filename = Str::slug(Siswa::find($request->siswa_id)->nama_lengkap) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/surat_izin', $filename);
                $newAbsensi->bukti_surat = $filename;
            }
            
            $newAbsensi->save();
            $message = 'Izin ' . ucfirst($request->status) . ' berhasil dicatat';
        }
        
        return redirect()->route('guru.izin.index')->with('success', $message);
    }

    /**
     * Menampilkan detail izin
     */
    public function show($id)
    {
        $guru = Auth::guard('guru')->user();
        $izin = Absensi::findOrFail($id);
        
        // Pastikan izin ini adalah milik guru yang mengakses
        if ($izin->guru_id != $guru->id) {
            return redirect()->route('guru.izin.index')->with('error', 'Anda tidak memiliki akses ke data ini');
        }
        
        return view('guru.izin.show', compact('izin'));
    }

    /**
     * Mencari siswa berdasarkan nama atau NISN
     */
    public function searchSiswa(Request $request)
    {
        $query = $request->q;
        $kelas_id = $request->kelas_id;
        
        $siswa = Siswa::where(function($q) use ($query) {
                $q->where('nama_lengkap', 'like', "%{$query}%")
                  ->orWhere('nisn', 'like', "%{$query}%");
            });
        
        if ($kelas_id) {
            $siswa->where('kelas_id', $kelas_id);
        }
        
        $results = $siswa->limit(10)->get();
        
        return response()->json([
            'results' => $results->map(function($s) {
                return [
                    'id' => $s->id,
                    'text' => $s->nama_lengkap . ' (' . $s->nisn . ')'
                ];
            })
        ]);
    }
    
    /**
     * Get students by class for select dropdown
     */
    public function getStudentsByClass(Request $request)
    {
        $kelas_id = $request->kelas_id;
        
        $siswa = Siswa::where('kelas_id', $kelas_id)
                ->orderBy('nama_lengkap')
                ->get();
                
        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }
}
