<?php

namespace App\Console\Commands;

use App\Imports\KelasImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportKelasCommand extends Command
{
    protected $signature = 'import:kelas {file} {--academic-year-id=}';
    protected $description = 'Import kelas data from CSV file';

    public function handle()
    {
        $file = $this->argument('file');
        $academicYearId = $this->option('academic-year-id');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        try {
            $import = new KelasImport($academicYearId);
            Excel::import($import, $file);

            $this->info('Kelas data import completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
