<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gurus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */    protected $fillable = [
        'username',
        'nip',
        'nama_lengkap',
        'email',
        'password',
        'jenis_kelamin',
        'no_telp',
        'alamat'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * Get the presensi records for the teacher.
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'guru_id');
    }

    /**
     * Get the teaching schedules for the teacher.
     */
    public function jadwalMengajar(): HasMany
    {
        return $this->hasMany(JadwalMengajar::class, 'guru_id');
    }
}