<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KelasTemplateExport;
use App\Models\AcademicYear;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran yang aktif');
        }
        
        $query = Kelas::with('waliKelas')
            ->where('academic_year_id', $activeYear->id);
            
        // Filter by tingkat if provided
        if ($request->has('tingkat') && $request->tingkat != '') {
            $query->where('tingkat', $request->tingkat);
        }
        
        $kelas = $query->get();
        $filterTingkat = $request->tingkat;
            
        return view('admin.kelas.index', compact('kelas', 'activeYear', 'filterTingkat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $guru = Guru::all();
        $academicYears = AcademicYear::where('is_active', true)->get();
        return view('admin.kelas.create', compact('guru', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|string|max:20',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        Kelas::create([
            'academic_year_id' => $request->academic_year_id,
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'wali_kelas_id' => $request->wali_kelas_id,
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kelas = Kelas::with(['waliKelas', 'siswa'])->findOrFail($id);
        return view('admin.kelas.show', compact('kelas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $guru = Guru::all();
        return view('admin.kelas.edit', compact('kelas', 'guru'));
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
        $kelas = Kelas::findOrFail($id);
        
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|string|max:20',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'wali_kelas_id' => $request->wali_kelas_id,
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus');
    }

    /**
     * Import data from an Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $activeYear = AcademicYear::where('is_active', true)->first();
            if (!$activeYear) {
                return redirect()->back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif']);
            }

            $import = new KelasImport($activeYear->id);
            Excel::import($import, $request->file('file'));
            
            return redirect()->route('admin.kelas.index')
                ->with('success', 'Data kelas berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal import data: ' . $e->getMessage()]);
        }
    }

    /**
     * Download the Excel template for importing kelas data.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return Excel::download(new KelasTemplateExport, 'template-import-kelas.xlsx');
    }
}
