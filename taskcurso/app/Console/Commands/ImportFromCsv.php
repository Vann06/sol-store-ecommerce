<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv 
                            {file : Path to the CSV file}
                            {table : Target database table}
                            {--delimiter=, : CSV delimiter (default: comma)}
                            {--skip-header : Skip the first row}
                            {--batch=100 : Batch size for bulk inserts}
                            {--dry-run : Preview without inserting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from CSV file to database table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $table = $this->argument('table');
        $delimiter = $this->option('delimiter');
        $skipHeader = $this->option('skip-header');
        $batchSize = (int) $this->option('batch');
        $dryRun = $this->option('dry-run');
        
        // Verificar que el archivo existe
        if (!file_exists($file)) {
            $this->error("❌ File not found: {$file}");
            return Command::FAILURE;
        }
        
        // Verificar que la tabla existe
        if (!$this->tableExists($table)) {
            $this->error("❌ Table '{$table}' does not exist");
            return Command::FAILURE;
        }
        
        $this->info("📊 Starting CSV import...");
        $this->newLine();
        
        try {
            // Leer CSV
            $data = $this->readCsv($file, $delimiter, $skipHeader);
            $totalRows = count($data);
            
            if ($totalRows === 0) {
                $this->warn("⚠️  CSV file is empty");
                return Command::SUCCESS;
            }
            
            $this->info("📖 File: {$file}");
            $this->info("📋 Table: {$table}");
            $this->info("📊 Rows: {$totalRows}");
            $this->newLine();
            
            // Preview de los primeros registros
            $this->info("📝 Preview (first 3 rows):");
            $this->table(
                array_keys($data[0]),
                array_slice($data, 0, 3)
            );
            $this->newLine();
            
            if ($dryRun) {
                $this->warn("🔍 DRY RUN MODE - No data will be inserted");
                return Command::SUCCESS;
            }
            
            // Confirmar
            if (!$this->confirm("Do you want to proceed with the import?")) {
                $this->info("❌ Import cancelled");
                return Command::SUCCESS;
            }
            
            // Importar datos
            $result = $this->importData($table, $data, $batchSize);
            
            $this->newLine();
            $this->info("✅ Import completed!");
            $this->info("   📥 Inserted: {$result['inserted']}");
            
            if ($result['errors'] > 0) {
                $this->warn("   ❌ Errors: {$result['errors']}");
                $this->warn("   Check logs for details");
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Fatal error: " . $e->getMessage());
            Log::error('CSV Import Error', [
                'file' => $file,
                'table' => $table,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
    
    /**
     * Read CSV file and return array of data
     */
    private function readCsv(string $file, string $delimiter, bool $skipHeader): array
    {
        $data = [];
        $headers = [];
        
        if (($handle = fopen($file, 'r')) !== false) {
            // Read headers
            $headers = fgetcsv($handle, 0, $delimiter);
            
            // Clean BOM if exists
            if (isset($headers[0])) {
                $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]);
            }
            
            if ($skipHeader) {
                // Skip first data row
                fgetcsv($handle, 0, $delimiter);
            }
            
            // Read data rows
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            
            fclose($handle);
        }
        
        return $data;
    }
    
    /**
     * Import data to database
     */
    private function importData(string $table, array $data, int $batchSize): array
    {
        $inserted = 0;
        $errors = 0;
        $batch = [];
        
        DB::beginTransaction();
        
        $progressBar = $this->output->createProgressBar(count($data));
        $progressBar->start();
        
        try {
            foreach ($data as $row) {
                try {
                    // Clean and prepare data
                    $cleanRow = [];
                    foreach ($row as $key => $value) {
                        $cleanKey = strtolower(trim($key));
                        $cleanRow[$cleanKey] = trim($value) === '' ? null : trim($value);
                    }
                    
                    // Add timestamps if they don't exist
                    if (!isset($cleanRow['created_at'])) {
                        $cleanRow['created_at'] = now();
                    }
                    if (!isset($cleanRow['updated_at'])) {
                        $cleanRow['updated_at'] = now();
                    }
                    
                    $batch[] = $cleanRow;
                    
                    // Insert batch
                    if (count($batch) >= $batchSize) {
                        DB::table($table)->insert($batch);
                        $inserted += count($batch);
                        $batch = [];
                    }
                    
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Row import error', [
                        'table' => $table,
                        'row' => $row,
                        'error' => $e->getMessage(),
                    ]);
                }
                
                $progressBar->advance();
            }
            
            // Insert remaining rows
            if (count($batch) > 0) {
                DB::table($table)->insert($batch);
                $inserted += count($batch);
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        $progressBar->finish();
        
        return [
            'inserted' => $inserted,
            'errors' => $errors,
        ];
    }
    
    /**
     * Check if table exists
     */
    private function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Exception $e) {
            return false;
        }
    }
}
