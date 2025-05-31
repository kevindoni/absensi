<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'academic_year_id',
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
        'is_active'
    ];

    /**
     * Get the wali kelas (classroom teacher) that owns the class.
     */
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    /**
     * Get all jadwal for this kelas.
     */
    public function jadwal()
    {
        return $this->hasMany(JadwalMengajar::class, 'kelas_id');
    }

    /**
     * Get all siswa for this kelas.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Get the jurusan (major) that owns the class.
     */
    // Comment out or remove jurusan relationship until database is ready
    /*
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }
    */
}
