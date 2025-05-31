<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'guru_id',
        'jadwal_id',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    /**
     * Relasi ke guru
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Relasi ke jadwal mengajar
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalMengajar::class, 'jadwal_id');
    }
}
