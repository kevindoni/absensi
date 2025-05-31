<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Get the active academic year
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get all siswa for this academic year
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'academic_year_id');
    }
}
