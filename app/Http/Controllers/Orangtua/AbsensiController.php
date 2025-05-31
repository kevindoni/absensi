<?php

namespace App\Http\Controllers\Orangtua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $orangtua = Auth::guard('orangtua')->user();
    $siswaIds = $orangtua->siswa->pluck('id');
    
    $absensi = Absensi::whereIn('siswa_id', $siswaIds)
            ->with(['siswa', 'jadwal.pelajaran', 'jadwal.kelas'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orangtua.absensi.index', compact('absensi'));
    }
}
