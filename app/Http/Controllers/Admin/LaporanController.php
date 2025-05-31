<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use View;

class LaporanController extends Controller
{    public function index(Request $request)
    {
        $kelas_id = $request->kelas_id;
        $tanggal_mulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-d');
        
        $query = Absensi::with(['siswa', 'siswa.kelas'])
            ->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            
        if ($kelas_id) {
            $query->whereHas('siswa', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }
          $data = $query->get();
          $summary = (object) [
            'total' => $data->count(),            'hadir' => $data->filter(function($item) {
                        return strtolower($item->status) === 'hadir' || strtolower($item->status) === 'terlambat';
                    })->count(),'izin' => $data->filter(function($item) {
                        return strtolower($item->status) === 'izin';
                    })->count(),
            'sakit' => $data->filter(function($item) {
                        return strtolower($item->status) === 'sakit';
                    })->count(),
            'alpa' => $data->filter(function($item) {
                        return strtolower($item->status) === 'alpha';
                    })->count(),
        ];
        
        // Handle Export
        if ($request->has('export')) {
            if ($request->export === 'excel') {
                // Change to make it more Excel-like
                return $this->exportExcel($data, $tanggal_mulai, $tanggal_akhir, $kelas_id);
            } elseif ($request->export === 'pdf') {
                // Change to make it more PDF-like
                return $this->exportPdf($data, $tanggal_mulai, $tanggal_akhir, $kelas_id, $summary);
            }
        }
        
        return view('admin.laporan.index', compact('data', 'summary'));
    }
    
    private function exportExcel($data, $tanggal_mulai, $tanggal_akhir, $kelas_id)
    {
        // Change file extension to .xls for better Excel compatibility
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="laporan_kehadiran_' . date('Ymd') . '.xls"',
            'Cache-Control' => 'max-age=0',
        ];
        
        $kelas = $kelas_id ? \App\Models\Kelas::find($kelas_id) : null;
        
        // Generate HTML that Excel can open
        $html = view('admin.laporan.excel', [
            'data' => $data,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'kelas' => $kelas
        ])->render();
        
        return response($html, 200, $headers);
    }

    private function exportPdf($data, $tanggal_mulai, $tanggal_akhir, $kelas_id, $summary)
    {
        $kelas = $kelas_id ? \App\Models\Kelas::find($kelas_id) : null;
        
        // Change approach - generate a print view that opens in a new tab
        $view = view('admin.laporan.print', [
            'data' => $data,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'kelas' => $kelas,
            'summary' => $summary,
            'isPrintMode' => true
        ])->render();

        // Return the view directly to open in a new tab with print dialog
        return new Response($view, 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
