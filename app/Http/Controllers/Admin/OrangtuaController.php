<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrangTua;
use App\Models\Siswa;

class OrangtuaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orangtua = OrangTua::with('siswa')->get();
        return view('admin.orangtua.index', compact('orangtua'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siswa = Siswa::whereNull('orangtua_id')->get();
        return view('admin.orangtua.create', compact('siswa'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    public function store(Request $request)
    {        $request->validate([
            'nama' => 'required|string|max:255',
            'siswa_id' => 'required|exists:siswas,id|unique:siswas,orangtua_id',
            'password' => 'required|string|min:6',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'required|string',
            'hubungan' => 'required|string|in:Ayah,Ibu,Wali'
        ]);
        $orangtua = OrangTua::create([
            'nama_lengkap' => $request->nama,
            'password' => bcrypt($request->password),
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'hubungan' => $request->hubungan
        ]);

        // Update the student's orangtua_id
        Siswa::where('id', $request->siswa_id)
            ->update(['orangtua_id' => $orangtua->id]);

        return redirect()->route('admin.orangtua.index')
            ->with('success', 'Data orangtua berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orangtua = OrangTua::with('siswa')->findOrFail($id);
        return view('admin.orangtua.show', compact('orangtua'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orangtua = OrangTua::findOrFail($id);
        $siswa = Siswa::all();
        return view('admin.orangtua.edit', compact('orangtua', 'siswa'));
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
        $orangtua = OrangTua::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'siswa_id' => [
                'required',
                'exists:siswas,id',
                function ($attribute, $value, $fail) use ($orangtua) {
                    $siswa = Siswa::find($value);
                    if ($siswa && $siswa->orangtua_id && $siswa->orangtua_id !== $orangtua->id) {
                        $fail('Siswa ini sudah memiliki orang tua terdaftar.');
                    }
                }
            ],            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'required|string',
            'hubungan' => 'required|string|in:Ayah,Ibu,Wali',
        ]);

        // If the parent had a previous student, remove the relationship
        $previousStudent = $orangtua->siswa;
        if ($previousStudent && $previousStudent->id != $request->siswa_id) {
            $previousStudent->orangtua_id = null;
            $previousStudent->save();
        }        
        // Update the parent's information
        $orangtua->update([
            'nama_lengkap' => $request->nama,
            'no_telp' => $request->no_telp ?? '', 
            'alamat' => $request->alamat ?? '', 
            'hubungan' => $request->hubungan,
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $orangtua->password = bcrypt($request->password);
            $orangtua->save();
        }

        // Update the student's parent relationship
        $siswa = Siswa::findOrFail($request->siswa_id);
        $siswa->orangtua_id = $orangtua->id;
        $siswa->save();

        return redirect()->route('admin.orangtua.index')
            ->with('success', 'Data orangtua berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orangtua = OrangTua::findOrFail($id);
        
        // Remove the parent relationship from any associated student
        if ($orangtua->siswa) {
            $orangtua->siswa->orangtua_id = null;
            $orangtua->siswa->save();
        }
        
        $orangtua->delete();

        return redirect()->route('admin.orangtua.index')
            ->with('success', 'Data orangtua berhasil dihapus');
    }
}
