<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function index()
    {
        $izinList = Izin::where('siswa_id', Auth::guard('siswa')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('siswa.izin.index', compact('izinList'));
    }

    public function create()
    {
        return view('siswa.izin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:izin,sakit',
            'keterangan' => 'required|string',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $izin = new Izin();
        $izin->siswa_id = Auth::guard('siswa')->id();
        $izin->tanggal = $request->tanggal;
        $izin->jenis = $request->jenis;
        $izin->keterangan = $request->keterangan;
        
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/bukti_izin', $filename);
            $izin->bukti = $filename;
        }
        
        $izin->save();

        return redirect()->route('siswa.izin.index')
            ->with('success', 'Permohonan izin berhasil diajukan');
    }

    public function show(Izin $izin)
    {
        if ($izin->siswa_id !== Auth::guard('siswa')->id()) {
            abort(403);
        }
        
        return view('siswa.izin.show', compact('izin'));
    }
}
