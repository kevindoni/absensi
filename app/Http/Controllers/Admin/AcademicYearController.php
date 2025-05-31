<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::orderBy('tanggal_mulai', 'desc')->get();
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return view('admin.academic_year.index', compact('academicYears', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return view('admin.academic_year.create', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:academic_years',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // If this academic year is set to active, deactivate all others
            if ($request->has('is_active') && $request->is_active) {
                AcademicYear::where('is_active', true)->update(['is_active' => false]);
            }
            
            // Create the new academic year
            AcademicYear::create([
                'nama' => $request->nama,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_active' => $request->has('is_active') ? $request->is_active : false,
            ]);
            
            DB::commit();
            return redirect()->route('admin.academic-year.index')
                ->with('success', 'Tahun Ajaran berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return view('admin.academic_year.show', compact('academicYear', 'settings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return view('admin.academic_year.edit', compact('academicYear', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:academic_years,nama,' . $academicYear->id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // If this academic year is set to active, deactivate all others
            if ($request->has('is_active') && $request->is_active) {
                AcademicYear::where('is_active', true)
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }
            
            // Update the academic year
            $academicYear->update([
                'nama' => $request->nama,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_active' => $request->has('is_active') ? $request->is_active : false,
            ]);
            
            DB::commit();
            return redirect()->route('admin.academic-year.index')
                ->with('success', 'Tahun Ajaran berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Only allow deletion if not active and has no associated data
        if ($academicYear->is_active) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus Tahun Ajaran yang sedang aktif.');
        }

        try {
            $academicYear->delete();
            return redirect()->route('admin.academic-year.index')
                ->with('success', 'Tahun Ajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Set an academic year as the active one
     */
    public function setActive(AcademicYear $academicYear)
    {
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Deactivate all academic years
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
            
            // Set the selected academic year as active
            $academicYear->update(['is_active' => true]);
            
            DB::commit();
            return redirect()->route('admin.academic-year.index')
                ->with('success', 'Tahun Ajaran ' . $academicYear->nama . ' telah diaktifkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Migrate students from one academic year to another
     */
    public function migrateStudents(Request $request)
    {
        $request->validate([
            'source_year_id' => 'required|exists:academic_years,id',
            'target_year_id' => 'required|exists:academic_years,id|different:source_year_id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'target_kelas_id' => 'required|exists:kelas,id',
        ]);

        // Begin transaction
        DB::beginTransaction();
        
        try {
            $sourceYearId = $request->source_year_id;
            $targetYearId = $request->target_year_id;
            $kelasId = $request->kelas_id;
            $targetKelasId = $request->target_kelas_id;
            
            $query = Siswa::where('academic_year_id', $sourceYearId);
            
            // Filter by class if provided
            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }
            
            // Get students to migrate
            $students = $query->get();
            
            if ($students->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada siswa yang dapat dimigrasikan dari tahun ajaran yang dipilih.');
            }
            
            // Update each student
            foreach ($students as $student) {
                $student->update([
                    'academic_year_id' => $targetYearId,
                    'kelas_id' => $targetKelasId,
                ]);
            }
            
            DB::commit();
            return redirect()->route('admin.academic-year.show', $targetYearId)
                ->with('success', $students->count() . ' siswa berhasil dimigrasikan ke tahun ajaran baru.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show migration form
     */
    public function showMigrateForm()
    {
        $academicYears = AcademicYear::orderBy('tanggal_mulai', 'desc')->get();
        $kelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        
        return view('admin.academic_year.migrate', compact('academicYears', 'kelas', 'settings'));
    }
}
