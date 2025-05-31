<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;
    
    protected $table = 'jurnal_mengajar';
    
    protected $fillable = [
        'tanggal',
        'jadwal_id',
        'guru_id',
        'materi',
        'kegiatan',
        'catatan',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
    ];
    
    public function jadwal()
    {
        return $this->belongsTo(JadwalMengajar::class);
    }
    
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
