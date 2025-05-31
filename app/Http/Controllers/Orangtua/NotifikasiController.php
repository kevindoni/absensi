<?php

namespace App\Http\Controllers\Orangtua;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DatabaseNotification;

class NotifikasiController extends Controller
{
    public function index()
    {
        $orangtua = Auth::guard('orangtua')->user();
        $notifications = $orangtua->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orangtua.notifikasi.index', compact('notifications'));
    }

    public function show($id)
    {
        $orangtua = Auth::guard('orangtua')->user();
        $notification = $orangtua->notifications()->findOrFail($id);
        
        return view('orangtua.notifikasi.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $orangtua = Auth::guard('orangtua')->user();
        $notification = $orangtua->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }
}
