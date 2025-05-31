<?php

namespace App\Http\Controllers\Orangtua;

use App\Http\Controllers\Controller;
use App\Models\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PesanController extends Controller
{
    public function index()
    {
        $orangtua = Auth::guard('orangtua')->user();
        $pesan = Pesan::where('orangtua_id', $orangtua->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orangtua.pesan.index', compact('pesan'));
    }

    public function create()
    {
        return view('orangtua.pesan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $orangtua = Auth::guard('orangtua')->user();
        
        Pesan::create([
            'orangtua_id' => $orangtua->id,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'status' => 'terkirim'
        ]);

        return redirect()->route('orangtua.pesan.index')
            ->with('success', 'Pesan berhasil dikirim');
    }

    public function show(Pesan $pesan)
    {
        if ($pesan->orangtua_id !== Auth::guard('orangtua')->id()) {
            abort(403);
        }
        
        $pesan->balasan_at = $pesan->balasan_at ? Carbon::parse($pesan->balasan_at) : null;
        $pesan->created_at = Carbon::parse($pesan->created_at);
        $pesan->updated_at = Carbon::parse($pesan->updated_at);
        
        return view('orangtua.pesan.show', compact('pesan'));
    }

    public function reply(Pesan $pesan)
    {
        if ($pesan->orangtua_id !== Auth::guard('orangtua')->id()) {
            abort(403);
        }
        
        $pesan->balasan_at = $pesan->balasan_at ? Carbon::parse($pesan->balasan_at) : null;
        return view('orangtua.pesan.reply', compact('pesan'));
    }

    public function storeReply(Request $request, Pesan $pesan)
    {
        if ($pesan->orangtua_id !== Auth::guard('orangtua')->id()) {
            abort(403);
        }

        $request->validate([
            'balasan' => 'required|string'
        ]);

        $pesan->update([
            'balasan' => $request->balasan,
            'balasan_at' => now(),
            'status' => 'dibalas'
        ]);

        return redirect()->route('orangtua.pesan.show', $pesan->id)
            ->with('success', 'Balasan berhasil dikirim');
    }

    public function end(Pesan $pesan)
    {
        if ($pesan->orangtua_id !== Auth::guard('orangtua')->id()) {
            abort(403);
        }

        $pesan->update(['status' => 'diakhiri']);
        
        return redirect()->route('orangtua.pesan.show', $pesan->id)
            ->with('success', 'Percakapan telah diakhiri');
    }
}
