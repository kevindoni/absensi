<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Pelajaran;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{
    /**
     * Display a listing of attendance reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        
        // Get available classes and subjects for this teacher
        $kelasList = Kelas::whereIn('id', function($query) use ($guru) {
            $query->select('kelas_id')
                  ->from('jadwal_mengajar')
                  ->where('guru_id', $guru->id)
                  ->distinct();
        })->get();
        
        $pelajaranList = Pelajaran::whereIn('id', function($query) use ($guru) {
            $query->select('pelajaran_id')
                  ->from('jadwal_mengajar')
                  ->where('guru_id', $guru->id)
                  ->distinct();
        })->get();
        
        // Get filter parameters
        $kelasId = $request->input('kelas_id');
        $pelajaranId = $request->input('pelajaran_id');
        $status = $request->input('status');
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(30)->toDateString());
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->toDateString());
          // Build the base query for both data and summary
        $baseQuery = Absensi::where('guru_id', $guru->id)
                 ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                 ->with(['siswa', 'siswa.kelas', 'jadwal', 'jadwal.pelajaran']);
                 
        // Apply filters if provided
        if ($kelasId) {
            $baseQuery->whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        
        if ($pelajaranId) {
            $baseQuery->whereHas('jadwal', function($q) use ($pelajaranId) {
                $q->where('pelajaran_id', $pelajaranId);
            });
        }
        
        if ($status) {
            $baseQuery->where('status', $status);
        }        
        // Get all data for summary calculation (clone the query before adding ordering)
        $allData = clone $baseQuery;
        $allData = $allData->get();
        
        // Get the data with pagination
        $perPage = $request->input('per_page', 100);  // Default to 100, but allow customization
        
        // Add ordering for display
        $baseQuery->orderBy('tanggal', 'desc')
                 ->orderBy('created_at', 'desc');
                 
        // Get paginated results
        $attendanceData = $baseQuery->paginate($perPage);
        
        // Append formatted_minutes_late attribute to each record
        $attendanceData->each(function($item) {
            $item->append('formatted_minutes_late');
        });
        
        // Calculate summary statistics
        $summary = [
            'hadir' => $allData->filter(function($item) {
                        return strtolower($item->status) === 'hadir' || strtolower($item->status) === 'terlambat';
                    })->count(),
            'izin' => $allData->filter(function($item) {
                        return strtolower($item->status) === 'izin';
                    })->count(),
            'sakit' => $allData->filter(function($item) {
                        return strtolower($item->status) === 'sakit';
                    })->count(),
            'alpha' => $allData->filter(function($item) {
                        return strtolower($item->status) === 'alpha';
                    })->count()
        ];
        
        return view('guru.laporan.index', compact(
            'attendanceData',
            'kelasList',
            'pelajaranList',
            'summary'
        ));
    }
    
    /**
     * Show detailed report for a specific period or export type
     * 
     * @param  string  $laporan
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($laporan, Request $request)
    {
        // If the parameter is "export", redirect to the export method
        if ($laporan === "export") {
            return $this->export($request);
        }

        // Otherwise, process it as a date or ID
        $guru = Auth::guard('guru')->user();
        
        try {
            // Try to parse it as a date
            $date = Carbon::parse($laporan);
            
            // Get all attendance records for this date
            $absensiData = Absensi::where('guru_id', $guru->id)
                           ->whereDate('tanggal', $date)
                           ->with(['siswa', 'siswa.kelas', 'jadwal', 'jadwal.pelajaran'])
                           ->orderBy('jadwal_id')
                           ->get();
            
            // Get journal entries for this date
            $jurnalData = JurnalMengajar::where('guru_id', $guru->id)
                          ->whereDate('tanggal', $date)
                          ->with(['jadwal', 'jadwal.pelajaran', 'jadwal.kelas'])
                          ->get();
            
            return view('guru.laporan.detail', compact('absensiData', 'jurnalData', 'date'));
        } catch (\Exception $e) {
            // If it's not a valid date, treat it as an ID
            return redirect()->route('guru.laporan.index')->with('error', 'Detail laporan tidak ditemukan');
        }
    }

    /**
     * Export attendance data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $exportType = $request->input('type', 'excel');
        
        // Fetch school settings from database
        $settings = DB::table('settings')->whereIn('key', [
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_website'
        ])->get()->pluck('value', 'key');
        
        // Create school info object from settings
        $sekolah = (object)[
            'nama' => $settings['school_name'] ?? 'SEKOLAH MENENGAH PERTAMA',
            'alamat' => $settings['school_address'] ?? '-',
            'telepon' => $settings['school_phone'] ?? '-',
            'email' => $settings['school_email'] ?? '-',
            'website' => $settings['school_website'] ?? '-',
            'kota' => explode(',', $settings['school_address'] ?? '')[0] ?? 'Jakarta' // Get city from address
        ];

        // Get filter parameters
        $startDate = Carbon::parse($request->input('tanggal_mulai', Carbon::now()->subDays(30)->toDateString()));
        $endDate = Carbon::parse($request->input('tanggal_akhir', Carbon::now()->toDateString()));
        
        // Get data for jurnal and attendance summary grouped by date and class
        $laporanAbsensi = JurnalMengajar::where('guru_id', $guru->id)
                          ->whereBetween('tanggal', [$startDate, $endDate])
                          ->with(['jadwal.kelas', 'jadwal.pelajaran'])
                          ->orderBy('tanggal')
                          ->get();
                          
        // Add attendance data for each journal entry
        foreach ($laporanAbsensi as $laporan) {
            // Get attendance data for this journal entry
            $absensi = Absensi::where('guru_id', $guru->id)
                      ->where('jadwal_id', $laporan->jadwal_id)
                      ->whereDate('tanggal', $laporan->tanggal)
                      ->get();
              // Calculate attendance summary
            $laporan->hadir = $absensi->whereIn('status', ['hadir', 'terlambat'])->count();
            $laporan->izin = $absensi->where('status', 'izin')->count();
            $laporan->sakit = $absensi->where('status', 'sakit')->count();
            $laporan->alpha = $absensi->where('status', 'alpha')->count();
            
            // Get total students in this class
            $kelas_id = $laporan->jadwal->kelas_id ?? null;
            $total_siswa = $kelas_id ? Siswa::where('kelas_id', $kelas_id)->count() : 0;
            $laporan->total_siswa = $total_siswa;
        }
        
        // Get unique subject names
        $mapel = JadwalMengajar::where('guru_id', $guru->id)
                ->whereIn('id', $laporanAbsensi->pluck('jadwal_id')->unique())
                ->with('pelajaran')
                ->get()
                ->pluck('pelajaran.nama_pelajaran')
                ->unique()
                ->filter()
                ->toArray();
        
        // Handle different export types
        switch ($exportType) {
            case 'excel':
                return $this->exportToExcel($laporanAbsensi, $guru, $startDate, $endDate, $mapel, $sekolah);
            
            case 'print':
            default:
                // Default to displaying the export view
                return view('guru.laporan.export', compact(
                    'laporanAbsensi', 'guru', 'startDate', 'endDate', 'mapel', 'sekolah'
                ));
        }
    }

    /**
     * Export data to Excel file
     */
    private function exportToExcel($laporanAbsensi, $guru, $startDate, $endDate, $mapel, $sekolah)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator($guru->nama_lengkap)
            ->setLastModifiedBy($guru->nama_lengkap)
            ->setTitle('Laporan Absensi')
            ->setSubject('Laporan Absensi Guru')
            ->setDescription('Laporan absensi untuk periode ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
        
        // Header - School Information
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $sekolah->nama);
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', $sekolah->alamat);
        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Telp: ' . $sekolah->telepon . ' | Email: ' . $sekolah->email);
        
        // Title
        $sheet->mergeCells('A5:G5');
        $sheet->setCellValue('A5', 'LAPORAN ABSENSI GURU');
        $sheet->mergeCells('A6:G6');
        $sheet->setCellValue('A6', 'Periode: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
        
        // Teacher Info
        $sheet->setCellValue('A8', 'Nama Guru');
        $sheet->setCellValue('B8', ': ' . $guru->nama_lengkap);
        $sheet->setCellValue('A9', 'NIP');
        $sheet->setCellValue('B9', ': ' . ($guru->nip ?? '-'));
        $sheet->setCellValue('A10', 'Mata Pelajaran');
        $sheet->setCellValue('B10', ': ' . implode(', ', $mapel));
        
        // Table Header
        $sheet->setCellValue('A12', 'No');
        $sheet->setCellValue('B12', 'Tanggal');
        $sheet->setCellValue('C12', 'Kelas');
        $sheet->setCellValue('D12', 'Mata Pelajaran');
        $sheet->setCellValue('E12', 'Materi');
        $sheet->setCellValue('F12', 'Hadir');
        $sheet->setCellValue('G12', 'Tidak Hadir');
        
        // Apply header style
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'],
            ],
        ];
        
        $sheet->getStyle('A12:G12')->applyFromArray($headerStyle);
        
        // Table Data
        $row = 13;
        foreach ($laporanAbsensi as $index => $laporan) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $laporan->tanggal->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $laporan->jadwal->kelas->nama_kelas ?? '-');
            $sheet->setCellValue('D' . $row, $laporan->jadwal->pelajaran->nama_pelajaran ?? '-');
            $sheet->setCellValue('E' . $row, $laporan->materi ?? '-');
            $sheet->setCellValue('F' . $row, $laporan->hadir);
            $sheet->setCellValue('G' . $row, $laporan->total_siswa - $laporan->hadir);
            $row++;
        }
        
        // If no data, add "No data" row
        if (count($laporanAbsensi) == 0) {
            $sheet->mergeCells('A13:G13');
            $sheet->setCellValue('A13', 'Tidak ada data absensi pada periode ini.');
            $row = 14;
        }
        
        // Apply table style
        $tableStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A12:G' . ($row - 1))->applyFromArray($tableStyle);
        
        // Signature
        $row += 2;
        $sheet->setCellValue('E' . $row, $sekolah->kota . ', ' . Carbon::now()->translatedFormat('d F Y'));
        $row++;
        $sheet->setCellValue('E' . $row, 'Guru Mata Pelajaran');
        $row += 4;
        $sheet->setCellValue('E' . $row, $guru->nama_lengkap);
        $row++;
        $sheet->setCellValue('E' . $row, 'NIP: ' . ($guru->nip ?? '-'));
        
        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Center align specific columns
        $sheet->getStyle('A13:A' . ($row - 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B13:B' . ($row - 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F13:G' . ($row - 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style for headers and title
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A8:A10')->getFont()->setBold(true);
        
        // Center align title
        $sheet->getStyle('A1:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-absensi-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        $path = storage_path('app/public/exports/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(storage_path('app/public/exports'))) {
            mkdir(storage_path('app/public/exports'), 0755, true);
        }
        
        $writer->save($path);
        
        // Return file download response
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Format minutes to a more readable hours and minutes format.
     *
     * @param int $minutes
     * @return string
     */
    private function formatMinutesLate($minutes)
    {
        $minutes = abs((int)$minutes);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($hours > 0 && $remainingMinutes > 0) {
            return $hours . " jam " . $remainingMinutes . " menit";
        } elseif ($hours > 0) {
            return $hours . " jam";
        } else {
            return $remainingMinutes . " menit";
        }
    }
}
