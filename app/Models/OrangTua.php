<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OrangTua extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orangtuas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */    
    protected $fillable = [
        'nama_lengkap',
        'password',
        'no_telp',
        'alamat',
        'hubungan'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];    /**
     * Get the student associated with the parent.
     */    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'orangtua_id');
    }
    
    /**
     * Accessor to get nama from nama_lengkap for backward compatibility.
     */
    public function getNamaAttribute()
    {
        return $this->nama_lengkap;
    }
    
    /**
     * Accessor to get no_hp from no_telp for backward compatibility.
     */
    public function getNoHpAttribute()
    {
        return $this->no_telp;
    }
    
    /**
     * Accessor to get formatted phone number for WhatsApp.
     */
    public function getNoHpFormattedAttribute()
    {
        $phone = $this->no_telp;
        if (!$phone) return null;
        
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        // If doesn't start with 62, add it
        elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}
