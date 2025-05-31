<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function update(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:gurus,email,' . $guru->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        $guru->update($request->only([
            'nama_lengkap',
            'email',
            'no_hp',
            'alamat'
        ]));

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password:guru',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::guard('guru')->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        $guru = Auth::guard('guru')->user();

        if ($guru->photo) {
            Storage::disk('public')->delete($guru->photo);
        }

        $path = $request->file('photo')->store('guru-photos', 'public');
        $guru->update(['photo' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui');
    }
}
