<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $siswa = Siswa::all();
        return view('admin.siswa.index', compact('siswa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas',
            'kelas_id' => 'required|exists:kelas,id',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
        ]);

        Siswa::create([
            'nama_lengkap' => $request->nama,
            'nisn' => $request->nisn,
            'kelas_id' => $request->kelas_id,
            'password' => bcrypt($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'qr_token' => Str::random(40) . time(),
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan');
    }/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    public function show(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
          // Start with a base query directly from absensis table
        $attendanceQuery = \App\Models\Absensi::where('siswa_id', $siswa->id)
            ->with(['jadwal.pelajaran', 'jadwal.guru']);
        
        // Apply date filters if set
        if ($request->has('from_date') && !empty($request->from_date)) {
            $attendanceQuery->whereDate('tanggal', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && !empty($request->to_date)) {
            $attendanceQuery->whereDate('tanggal', '<=', $request->to_date);
        }
        
        // Apply status filter if set
        if ($request->has('status') && !empty($request->status)) {
            $attendanceQuery->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }
        
        // Get the paginated results
        $attendanceHistory = $attendanceQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));// Get all attendance records from both absensis and absensi_details tables for debugging
        $directRecords = \App\Models\Absensi::where('siswa_id', $siswa->id)->get();
        $detailRecords = $siswa->absensiDetails()->with('absensi')->get();
        
        // Calculate attendance statistics with case-insensitive comparison from direct absensis table
        $attendanceStats = [
            'hadir' => \App\Models\Absensi::where('siswa_id', $siswa->id)->whereRaw('LOWER(status) = ?', ['hadir'])->count(),
            'izin' => \App\Models\Absensi::where('siswa_id', $siswa->id)->whereRaw('LOWER(status) = ?', ['izin'])->count(),
            'sakit' => \App\Models\Absensi::where('siswa_id', $siswa->id)->whereRaw('LOWER(status) = ?', ['sakit'])->count(), 
            'alpha' => \App\Models\Absensi::where('siswa_id', $siswa->id)->whereRaw('LOWER(status) = ?', ['alpha'])->count(),
            'total' => \App\Models\Absensi::where('siswa_id', $siswa->id)->count()
        ];
          // Add debug info to session for checking
        session()->flash('debug_info', [
            'direct_has_records' => $directRecords->count() > 0,
            'direct_record_count' => $directRecords->count(),
            'direct_records' => $directRecords->map(function($record) {
                return [
                    'id' => $record->id,
                    'status' => $record->status,
                    'date' => $record->tanggal ? $record->tanggal->format('Y-m-d') : 'N/A',
                    'siswa_id' => $record->siswa_id,
                ];
            }),
            'detail_has_records' => $detailRecords->count() > 0,
            'detail_record_count' => $detailRecords->count(),
            'detail_records' => $detailRecords->map(function($record) {
                return [
                    'id' => $record->id,
                    'status' => $record->status,
                    'date' => optional($record->absensi)->tanggal ? $record->absensi->tanggal->format('Y-m-d') : 'N/A',
                    'absensi_id' => $record->absensi_id,
                ];
            })
        ]);
          // Get monthly attendance data for the chart
        $monthlyAttendance = $this->getMonthlyAttendance($siswa, $request);
        
        return view('admin.siswa.show', compact('siswa', 'attendanceHistory', 'attendanceStats', 'monthlyAttendance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn,'.$id,
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $siswa->update([
            'nama_lengkap' => $request->nama,
            'nisn' => $request->nisn,
            'kelas_id' => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $siswa->password = bcrypt($request->password);
            $siswa->save();
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus');
    }

    /**
     * Search for students by name or NISN
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $kelas_id = $request->input('kelas_id');
        
        $siswa = Siswa::where(function($q) use ($query) {
                $q->where('nama_lengkap', 'like', "%{$query}%")
                  ->orWhere('nisn', 'like', "%{$query}%");
            })
            ->with('kelas')
            ->orderBy('nama_lengkap');
            
        if ($kelas_id) {
            $siswa->where('kelas_id', $kelas_id);
        }
        
        $data = $siswa->limit(10)->get();
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Import students from an Excel file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $activeYear = AcademicYear::where('is_active', true)->first();
            if (!$activeYear) {
                return redirect()->back()->with('error', 'Tidak ada tahun akademik yang aktif.');
            }

            \Log::info('Starting student import process', [
                'filename' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'type' => $request->file('file')->getMimeType(),
                'academic_year' => $activeYear->year
            ]);

            $startTime = microtime(true);
            
            $import = new SiswaImport($activeYear);
            Excel::import($import, $request->file('file'));
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            \Log::info('Student import completed successfully', [
                'execution_time' => $executionTime,
                'records_processed' => $import->getSuccessCount(),
                'errors' => $import->getErrorCount()
            ]);

            $successMessage = sprintf(
                'Import berhasil! %d siswa telah ditambahkan dalam %.2f detik. Tahun Ajaran: %s', 
                $import->getSuccessCount(), 
                $executionTime,
                $activeYear->year
            );

            return redirect()->route('admin.siswa.index')->with('success', $successMessage);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            \Log::error('Validation error during import', [
                'failures' => $e->failures(),
                'error_count' => count($e->failures())
            ]);
            
            $errors = collect($e->failures())
                ->map(function($failure) {
                    return "Baris {$failure->row()}: {$failure->errors()[0]}";
                })
                ->all();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan validasi pada data import:')
                ->with('validation_errors', $errors);

        } catch (\Exception $e) {
            \Log::error('Error during student import', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template for student import
     *
     * @return \Illuminate\Http\Response
     */    public function downloadTemplate()
    {
        try {
            return Excel::download(new SiswaTemplateExport, 'template_import_siswa.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }
      /**
     * Get monthly attendance data for the past 6 months
     * 
     * @param Siswa $siswa
     * @param Request $request
     * @return array
     */
    private function getMonthlyAttendance(Siswa $siswa, Request $request)
    {
        // Default to last 6 months
        $startDate = now()->subMonths(5)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        // Use date range from filters if provided
        if ($request->has('from_date') && !empty($request->from_date)) {
            $filterStartDate = \Carbon\Carbon::parse($request->from_date);
            
            // Only use if it's within the last 12 months (to ensure we have data for chart)
            if ($filterStartDate >= now()->subMonths(12)) {
                $startDate = $filterStartDate->startOfMonth();
            }
        }
        
        if ($request->has('to_date') && !empty($request->to_date)) {
            $filterEndDate = \Carbon\Carbon::parse($request->to_date);
            $endDate = $filterEndDate->endOfMonth();
            
            // Ensure we're not showing more than 6 months for readability
            if ($startDate->diffInMonths($endDate) > 5) {
                $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            }
        }        // Get all attendance details directly from absensis table
        $attendanceQuery = \App\Models\Absensi::where('siswa_id', $siswa->id);
        
        // Apply date filter
        if ($startDate && $endDate) {
            $attendanceQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }
            
        // Apply status filter if set
        if ($request->has('status') && !empty($request->status)) {
            $attendanceQuery->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }
            
        // Get attendance details
        $attendances = $attendanceQuery->get();
          // For debugging, add info about the attendance records found
        session()->flash('monthly_debug', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'), 
            'record_count' => $attendances->count(),
            'all_dates' => $attendances->map(function($record) {
                return $record->tanggal ? $record->tanggal->format('Y-m-d') : 'N/A';
            })->toArray()
        ]);
          // Initialize the result array with months in the date range
        $months = [];
        $labels = [];
        
        // Clone dates to avoid modifying original
        $currentDate = $startDate->copy();
        $endMonthDate = $endDate->copy();
        
        // Generate all months in the range
        while ($currentDate <= $endMonthDate) {
            $monthKey = $currentDate->format('Y-m');
            $monthLabel = $currentDate->format('M Y');
            
            $months[$monthKey] = [
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpha' => 0
            ];
            $labels[] = $monthLabel;
            
            $currentDate->addMonth();
        }        // Count attendance by status and month
        foreach ($attendances as $attendance) {
            // Skip if no tanggal value
            if (!$attendance->tanggal) {
                continue;
            }
            
            $month = $attendance->tanggal->format('Y-m');
            
            // Skip if the month is not in our range
            if (!isset($months[$month])) {
                continue;
            }
            
            // Normalize status to lowercase for consistent counting
            $status = strtolower($attendance->status);
            
            // Increment the appropriate status count
            if (isset($months[$month][$status])) {
                $months[$month][$status]++;
            }
        }
          // Format the data for Chart.js
        $hadir = [];
        $izin = [];
        $sakit = [];
        $alpha = [];
        $percentages = [];
        
        foreach ($months as $month => $data) {
            $hadir[] = $data['hadir'];
            $izin[] = $data['izin'];
            $sakit[] = $data['sakit'];
            $alpha[] = $data['alpha'];
            
            // Calculate attendance percentage (hadir count / total count)
            $total = $data['hadir'] + $data['izin'] + $data['sakit'] + $data['alpha'];
            $percentage = $total > 0 ? round(($data['hadir'] / $total) * 100, 1) : 0;
            $percentages[] = $percentage;
        }
        
        return [
            'labels' => $labels,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha,
            'percentages' => $percentages
        ];
    }
}
