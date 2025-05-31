<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelajaran extends Model
{
    use HasFactory;
    
    protected $table = 'pelajaran';
    
    protected $fillable = [
        'nama_pelajaran',
        'kode_pelajaran',
        'deskripsi'
    ];
    
    public function jadwal()
    {
        return $this->hasMany(JadwalMengajar::class, 'pelajaran_id');
    }
}
