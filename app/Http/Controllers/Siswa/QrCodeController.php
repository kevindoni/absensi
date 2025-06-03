<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    }

    /**
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

        // Return SVG content for download
        return response($qrSvg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $downloadName . '"');
    }
}
