<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Izin extends Model
{
    protected $table = 'izin';
    
    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jenis',
        'keterangan',
        'bukti',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
