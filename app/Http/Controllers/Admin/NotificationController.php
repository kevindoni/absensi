<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = auth()->guard('admin')->user()->notifications()->latest()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request)
    {
        auth()->guard('admin')->user()->notifications->find($request->id)->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        auth()->guard('admin')->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai telah dibaca.');
    }
}
