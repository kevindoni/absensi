<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'orangtua_id',
        'judul',
        'pesan',
        'tipe',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    public function orangtua()
    {
        return $this->belongsTo(OrangTua::class);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }
}
