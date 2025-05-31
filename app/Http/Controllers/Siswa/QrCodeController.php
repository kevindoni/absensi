<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    /**
     * Show the QR code page.
     */
    public function show()
    {
        $siswa = Auth::guard('siswa')->user();
        return view('siswa.qrcode.show', compact('siswa'));
    }/**
     * Download the QR code image file.
     * Uses standardized QR generation to avoid ImageMagick issues.
     */
    public function download()
    {
        $siswa = Auth::guard('siswa')->user();
        
        // Ensure student has standardized QR token
        if (!$siswa->qr_token || strlen($siswa->qr_token) !== 50) {
            $siswa->qr_token = \Illuminate\Support\Str::random(40) . time();
            $siswa->qr_generated_at = now();
            $siswa->save();
        }

        // Generate SVG QR code using standardized method
        $qrSvg = \App\Http\Controllers\QrController::generateStandardQrCode($siswa, 400);
        
        $cleanName = str_replace(' ', '_', strtolower($siswa->nama_lengkap));
        $downloadName = 'qrcode-' . $cleanName . '.svg';

        // Return SVG as download (more reliable than PNG with ImageMagick issues)
        return response($qrSvg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $downloadName . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
