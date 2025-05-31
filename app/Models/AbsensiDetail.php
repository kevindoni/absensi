<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiDetail extends Model
{
    use HasFactory;
    
    protected $table = 'absensi_details';
    
    protected $fillable = [
        'absensi_id',
        'siswa_id',
        'status',
        'keterangan',
        'scan_time',
        'minutes_late',
        'is_valid_attendance'
    ];
    
    protected $casts = [
        'scan_time' => 'datetime'
    ];
    
    /**
     * Get the absensi that owns this detail.
     */
    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }
    
    /**
     * Get the siswa associated with this detail.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
