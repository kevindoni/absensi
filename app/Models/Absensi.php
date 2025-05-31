<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensis';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */    protected $fillable = [
        'tanggal',
        'jadwal_id',
        'guru_id',
        'siswa_id',
        'status',
        'minutes_late',
        'is_valid_attendance',
        'keterangan',
        'is_completed'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */    protected $casts = [
        'tanggal' => 'date',
        'is_completed' => 'boolean',
        'is_valid_attendance' => 'boolean',
        'minutes_late' => 'integer'
    ];
    
    /**
     * Mutator to standardize the status attribute to lowercase when saving to database.
     *     * @param  string  $value
     * @return void
     */
     
    /**
     * Format minutes late to a human-readable string.
     *
     * @return string
     */
    public function getFormattedMinutesLateAttribute()
    {
        $minutes = abs($this->minutes_late);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($hours > 0 && $remainingMinutes > 0) {
            return $hours . ' jam ' . $remainingMinutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $remainingMinutes . ' menit';
        }
    }
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }
    
    /**
     * Accessor to get a standardized status value.
     *
     * @param string $value
     * @return string
     */
    public function getStatusAttribute($value)
    {
        return $value;  // Return as is, we'll handle display casing in views
    }
    
    /**
     * Get the guru that owns the absensi.
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }    /**
     * Get the siswa that owns the absensi.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    
    /**
     * Get the jadwal that owns the absensi.
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalMengajar::class, 'jadwal_id');
    }
    
    /**
     * Get the details for this absensi.
     */
    public function details()
    {
        return $this->hasMany(AbsensiDetail::class);
    }
}
