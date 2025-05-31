<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model
{
    use HasFactory;
    
    protected $table = 'jadwal_mengajar';
    
    protected $fillable = [
        'guru_id',
        'kelas_id',
        'pelajaran_id',
        'hari',
        'jam_ke',
        'jam_ke_list',
        'jam_mulai',
        'jam_selesai',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'jam_ke_list' => 'array',
    ];
    
    /**
     * Get the nama_hari attribute.
     *
     * @return string
     */
    public function getNamaHariAttribute()
    {
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return $hari[$this->hari] ?? '';
    }
    
    /**
     * Get periods list as a formatted string.
     *
     * @return string
     */
    public function getPeriodsAttribute()
    {
        if (!empty($this->jam_ke_list)) {
            $periods = $this->jam_ke_list;
            if (count($periods) > 1) {
                return 'Jam ke-' . min($periods) . ' s/d ' . max($periods);
            } else {
                return 'Jam ke-' . $periods[0];
            }
        }
        
        return 'Jam ke-' . ($this->jam_ke ?: '?');
    }
    
    /**
     * Get the kelas for this jadwal.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    
    /**
     * Get the guru for this jadwal.
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
    
    /**
     * Get the pelajaran for this jadwal.
     */
    public function pelajaran()
    {
        return $this->belongsTo(Pelajaran::class);
    }
}
