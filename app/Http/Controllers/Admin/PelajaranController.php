<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelajaran;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PelajaranImport;
use App\Exports\PelajaranTemplateExport;

class PelajaranController extends Controller
{
    public function index()
    {
        $pelajaran = Pelajaran::all();
        return view('admin.pelajaran.index', compact('pelajaran'));
    }
    
    public function create()
    {
        return view('admin.pelajaran.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelajaran' => 'required|string|max:255',
            'kode_pelajaran' => 'nullable|string|max:20|unique:pelajaran',
            'deskripsi' => 'nullable|string',
        ]);
        
        Pelajaran::create($request->all());
        
        return redirect()->route('admin.pelajaran.index')->with('success', 'Data pelajaran berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        $pelajaran = Pelajaran::findOrFail($id);
        return view('admin.pelajaran.edit', compact('pelajaran'));
    }
    
    public function update(Request $request, $id)
    {
        $pelajaran = Pelajaran::findOrFail($id);
        
        $request->validate([
            'nama_pelajaran' => 'required|string|max:255',
            'kode_pelajaran' => 'nullable|string|max:20|unique:pelajaran,kode_pelajaran,'.$id,
            'deskripsi' => 'nullable|string',
        ]);
        
        $pelajaran->update($request->all());
        
        return redirect()->route('admin.pelajaran.index')->with('success', 'Data pelajaran berhasil diupdate');
    }
    
    public function destroy($id)
    {
        $pelajaran = Pelajaran::findOrFail($id);
        $pelajaran->delete();
        
        return redirect()->route('admin.pelajaran.index')->with('success', 'Data pelajaran berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $startTime = microtime(true);
            
            $import = new PelajaranImport();
            Excel::import($import, $request->file('file'));
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            \Log::info('Subject import completed successfully', [
                'execution_time' => $executionTime,
                'records_processed' => $import->getRowCount(),
                'errors' => $import->failures ? count($import->failures) : 0
            ]);

            $successMessage = sprintf(
                'Import berhasil! %d mata pelajaran telah ditambahkan dalam %.2f detik.', 
                $import->getRowCount(), 
                $executionTime
            );

            return redirect()->route('admin.pelajaran.index')->with('success', $successMessage);

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
            \Log::error('Error during subject import', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new PelajaranTemplateExport, 'template_import_pelajaran.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }
}
