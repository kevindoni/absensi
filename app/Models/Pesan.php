<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesan extends Model
{
    use HasFactory;

    protected $table = 'pesans';

    const STATUS_TERKIRIM = 'terkirim';
    const STATUS_DIBACA_ADMIN = 'dibaca_admin';
    const STATUS_DIBALAS_ADMIN = 'dibalas_admin';
    const STATUS_DIBALAS_ORANGTUA = 'dibalas_orangtua';
    const STATUS_DIAKHIRI = 'diakhiri';

    protected $fillable = [
        'orangtua_id',
        'judul',
        'isi',
        'status',
        'balasan',
        'balasan_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'balasan_at'
    ];

    protected $casts = [
        'balasan_at' => 'datetime',
    ];

    public function orangtua()
    {
        return $this->belongsTo(OrangTua::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_TERKIRIM => 'warning',
            self::STATUS_DIBACA_ADMIN => 'info',
            self::STATUS_DIBALAS_ADMIN => 'success',
            self::STATUS_DIBALAS_ORANGTUA => 'primary',
            self::STATUS_DIAKHIRI => 'secondary',
            default => 'light'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_TERKIRIM => 'Terkirim',
            self::STATUS_DIBACA_ADMIN => 'Dibaca Admin',
            self::STATUS_DIBALAS_ADMIN => 'Dibalas Admin',
            self::STATUS_DIBALAS_ORANGTUA => 'Dibalas Orangtua',
            self::STATUS_DIAKHIRI => 'Diakhiri',
            default => 'Unknown'
        };
    }
}
