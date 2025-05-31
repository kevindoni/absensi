<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicYear;
use App\Models\Kelas;

class CreateClass extends Command
{
    protected $signature = 'class:create {name} {tingkat}';
    protected $description = 'Create a new class in the active academic year';

    public function handle()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            $this->error('No active academic year found.');
            return 1;
        }

        try {
            Kelas::create([
                'academic_year_id' => $activeYear->id,
                'nama_kelas' => $this->argument('name'),
                'tingkat' => $this->argument('tingkat')
            ]);

            $this->info("Class {$this->argument('name')} created successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create class: " . $e->getMessage());
            return 1;
        }
    }
}
