<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $absensi = Absensi::where('siswa_id', $siswa->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        
        return view('siswa.absensi.index', compact('absensi'));
    }

    public function show(Absensi $absensi)
    {
        if ($absensi->siswa_id !== Auth::guard('siswa')->id()) {
            abort(403);
        }
        
        return view('siswa.absensi.show', compact('absensi'));
    }
}
