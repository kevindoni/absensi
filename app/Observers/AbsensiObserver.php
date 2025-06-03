<?php

namespace App\Observers;

use App\Models\Absensi;

class AbsensiObserver
{
    /**
     * Handle the Absensi "created" event.
     */
    public function created(Absensi $absensi): void
    {
        // Absensi record created - can add custom logic here if needed
    }

    /**
     * Handle the Absensi "updated" event.
     */
    public function updated(Absensi $absensi): void
    {
        // Absensi record updated - can add custom logic here if needed
    }
}
