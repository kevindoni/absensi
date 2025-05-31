<?php

namespace App\Observers;

use App\Models\Absensi;
use Illuminate\Support\Facades\Log;

class AbsensiObserver
{
    /**
     * Handle the Absensi "created" event.
     */
    public function created(Absensi $absensi): void
    {
        // Absensi record created - can add custom logic here if needed
        Log::info('Absensi record created', [
            'absensi_id' => $absensi->id,
            'siswa_id' => $absensi->siswa_id
        ]);
    }

    /**
     * Handle the Absensi "updated" event.
     */
    public function updated(Absensi $absensi): void
    {
        // Absensi record updated - can add custom logic here if needed
        Log::info('Absensi record updated', [
            'absensi_id' => $absensi->id,
            'siswa_id' => $absensi->siswa_id,
            'status' => $absensi->status
        ]);
    }
}
