<?php

namespace App\Console\Commands;

use App\Imports\GuruImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportGuruCommand extends Command
{
    protected $signature = 'import:guru {file}';
    protected $description = 'Import guru data from CSV file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        try {
            $import = new GuruImport();
            Excel::import($import, $file);

            if (count($import->failures)) {
                $this->warn('Some rows failed to import:');
                foreach ($import->failures as $failure) {
                    $this->error("Row {$failure->row()}: " . implode(', ', $failure->errors()));
                }
            }

            $this->info('Guru data import completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
