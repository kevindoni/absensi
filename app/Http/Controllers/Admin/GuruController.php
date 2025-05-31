<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GuruTemplateExport;
use App\Imports\GuruImport;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guru = Guru::all();
        return view('admin.guru.index', compact('guru'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'username' => 'required|string|unique:gurus,username',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:gurus,email',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'required|string', // Changed to required since the database column doesn't allow NULL
        ]);Guru::create([
            'nama_lengkap' => $request->nama_lengkap, // Map nama_lengkap form field to nama database column
            'nip' => $request->nip ?: null, // Use null instead of empty string
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telp' => $request->no_hp ?: null, // Use null instead of empty string (note: field is no_telp, not no_hp)
            'alamat' => $request->alamat ?: '-', // Use a dash instead of null for required field
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.show', compact('guru'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
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
        $guru = Guru::findOrFail($id);        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:gurus,username,'.$id,
            'email' => 'required|email|unique:gurus,email,'.$id,
            'nip' => 'nullable|string|max:20',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'required|string', // Changed to required since the database column doesn't allow NULL
            'jenis_kelamin' => 'required|in:L,P',
        ]);        $guru->update([
            'nama_lengkap' => $request->nama_lengkap, // Map nama form field to nama_lengkap database column
            'username' => $request->username,
            'nip' => $request->nip ?: null, // Use null instead of empty string
            'email' => $request->email,
            'no_telp' => $request->no_telp ?: null, // Use null instead of empty string
            'alamat' => $request->alamat ?: '-', // Use a dash instead of null for required field
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $guru->password = bcrypt($request->password);
            $guru->save();
        }

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil dihapus');
    }

    /**
     * Download the template for importing guru data.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        try {
            return Excel::download(new GuruTemplateExport, 'template_import_guru.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }

    /**
     * Import guru data from an Excel file.
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
            Excel::import(new GuruImport, $request->file('file'));
            return redirect()->route('admin.guru.index')
                ->with('success', 'Data guru berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal import data: ' . $e->getMessage()]);
        }
    }
}
