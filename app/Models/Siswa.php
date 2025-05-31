<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Siswa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'siswas';

    protected $fillable = [
        'nisn',
        'password',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas_id',
        'orangtua_id',
        'tanggal_lahir',
        'alamat',
        'qr_token',
    ];    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'qr_generated_at' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }    public function orangtua()
    {
        return $this->belongsTo(OrangTua::class, 'orangtua_id');
    }
    
    /**
     * Get the attendance details for this student.
     */
    public function absensiDetails()
    {
        return $this->hasMany(AbsensiDetail::class, 'siswa_id');
    }    /**
     * Generate QR Code image file as SVG and return full file path.
     * Uses standardized QR generation method from QrController.
     * Always creates SVG to avoid ImageMagick issues with PNG.
     * 
     * @param string $format 'svg' (PNG parameter is ignored and SVG is always used)
     * @return string full path to generated SVG file
     */
    public function generateQrCode($format = 'svg')
    {
        // Ensure student has standardized QR token
        if (!$this->qr_token || strlen($this->qr_token) !== 50) {
            $this->qr_token = \Illuminate\Support\Str::random(40) . time();
            $this->qr_generated_at = now();
            $this->save();
        }

        // Use standardized QR generation (always SVG)
        $qrSvg = \App\Http\Controllers\QrController::generateStandardQrCode($this);

        $cleanFileName = str_replace(' ', '_', strtolower($this->nama_lengkap));
        
        // Always use SVG format regardless of requested format to avoid ImageMagick errors
        $image = $qrSvg;
        $fileName = "qrcodes/siswa-{$cleanFileName}.svg";
        
        // Log if PNG was requested but SVG was used instead
        if ($format === 'png') {
            \Log::info("PNG QR code requested for student {$this->id}, using SVG instead to avoid ImageMagick errors");
        }

        $directory = storage_path('app/public/qrcodes');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        Storage::disk('public')->put($fileName, $image);
        return storage_path('app/public/' . $fileName);
    }/**
     * Accessor to get inline base64 encoded SVG QR code for this siswa.
     * Uses standardized QR generation method from QrController.
     */
    public function getQrCodeAttribute()
    {
        // Ensure student has standardized QR token
        if (!$this->qr_token || strlen($this->qr_token) !== 50) {
            $this->qr_token = \Illuminate\Support\Str::random(40) . time();
            $this->qr_generated_at = now();
            $this->save();
        }

        // Use standardized QR generation
        $svg = \App\Http\Controllers\QrController::generateStandardQrCode($this);

        return "data:image/svg+xml;base64," . base64_encode($svg);
    }
}
