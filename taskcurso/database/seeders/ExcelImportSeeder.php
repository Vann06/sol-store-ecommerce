<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExcelImportSeeder extends Seeder
{
    /**
     * Seeder para importar datos desde archivos CSV/Excel
     * 
     * USO:
     * 1. Exporta tu Excel a CSV (separado por comas)
     * 2. Coloca el archivo en storage/app/imports/
     * 3. Ejecuta: php artisan db:seed --class=ExcelImportSeeder
     * 
     * IMPORTANTE: Los archivos se importan en orden:
     * 1. Categories (categorías)
     * 2. Themes (temáticas)
     * 3. Materiales
     * 4. Usuarios
     * 5. Productos (requiere categories y themes)
     */
    public function run(): void
    {
        // ============ CONFIGURACIÓN ============
        $importConfigs = [
            // 1. Importar categorías (debe ir primero)
            'categories' => [
                'file' => 'storage/app/imports/1_categories.csv',
                'table' => 'categories',
                'mapping' => [
                    'name' => 'name',
                    'imagen' => 'imagen',
                ],
                'simple' => true, // No requiere transformaciones ni relaciones
            ],
            
            // 2. Importar temáticas (debe ir antes de productos)
            'themes' => [
                'file' => 'storage/app/imports/2_themes.csv',
                'table' => 'themes',
                'mapping' => [
                    'name' => 'name',
                    'imagen' => 'imagen',
                ],
                'simple' => true,
            ],
            
            // 3. Importar materiales (debe ir antes de atributos)
            'materiales' => [
                'file' => 'storage/app/imports/3_materiales.csv',
                'table' => 'materiales',
                'mapping' => [
                    'nombre' => 'nombre',
                ],
                'simple' => true,
            ],
            
            // 4. Importar usuarios (opcional, para created_by)
            'usuarios' => [
                'file' => 'storage/app/imports/4_usuarios.csv',
                'table' => 'usuarios',
                'mapping' => [
                    'first_name' => 'first_name',
                    'last_name' => 'last_name',
                    'email' => 'email',
                    'password' => 'password',
                ],
                'transforms' => [
                    'email' => fn($value) => strtolower(trim($value)),
                ],
                'simple' => true,
            ],
            
            // 5. Importar productos (requiere categories y themes)
            'productos' => [
                'file' => 'storage/app/imports/5_productos.csv',
                'table' => 'productos',
                'mapping' => [
                    'nombre' => 'nombre',
                    'categoria' => 'id_categoria',
                    'tematica' => 'id_tematica',
                    'descripcion' => 'descripcion',
                    'precio_base' => 'precio_base',
                    'stock' => 'stock',
                    'imagen' => 'imagen',
                    'status' => 'status',
                ],
                'transforms' => [
                    'precio_base' => fn($value) => floatval(str_replace(',', '', $value)),
                    'stock' => fn($value) => intval($value),
                ],
                'relations' => [
                    'id_categoria' => ['table' => 'categories', 'column' => 'name'],
                    'id_tematica' => ['table' => 'themes', 'column' => 'name'],
                ],
            ],
        ];
        
        // Seleccionar qué importar (en orden de dependencias)
        $toImport = [
            'categories',   // Primero categorías
            'themes',       // Luego temáticas
            'materiales',   // Materiales para atributos
            'usuarios',     // Usuarios (opcional)
            'productos',    // Finalmente productos
        ];
        
        // ============ PROCESO DE IMPORTACIÓN ============
        foreach ($toImport as $importKey) {
            if (!isset($importConfigs[$importKey])) {
                $this->command->error("⚠️  Configuración '$importKey' no encontrada");
                continue;
            }
            
            $config = $importConfigs[$importKey];
            $this->importFromCSV($config);
        }
    }
    
    /**
     * Importa datos desde un archivo CSV
     */
    private function importFromCSV(array $config): void
    {
        $filePath = base_path($config['file']);
        $tableName = $config['table'];
        
        $this->command->info("📊 Importando {$tableName}...");
        
        if (!file_exists($filePath)) {
            $this->command->error("❌ Archivo no encontrado: {$filePath}");
            return;
        }
        
        try {
            $data = $this->readCSV($filePath);
            $totalRows = count($data);
            
            if ($totalRows === 0) {
                $this->command->warn("⚠️  El archivo está vacío");
                return;
            }
            
            $this->command->info("   Filas encontradas: {$totalRows}");
            
            DB::beginTransaction();
            
            $progressBar = $this->command->getOutput()->createProgressBar($totalRows);
            $progressBar->start();
            
            $inserted = 0;
            $errors = 0;
            
            foreach ($data as $row) {
                try {
                    // Mapear y transformar datos
                    $mappedData = $this->mapRow($row, $config);
                    
                    // Resolver relaciones
                    if (isset($config['relations'])) {
                        $mappedData = $this->resolveRelations($mappedData, $row, $config);
                    }
                    
                    // Agregar valores por defecto
                    if (isset($config['defaults'])) {
                        $mappedData = array_merge($config['defaults'], $mappedData);
                    }
                    
                    // Agregar timestamps
                    if (!isset($mappedData['created_at'])) {
                        $mappedData['created_at'] = now();
                        $mappedData['updated_at'] = now();
                    }
                    
                    // Insertar
                    DB::table($tableName)->insert($mappedData);
                    $inserted++;
                    
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Error importando fila: " . $e->getMessage(), [
                        'table' => $tableName,
                        'row' => $row,
                    ]);
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            DB::commit();
            
            $this->command->newLine(2);
            $this->command->info("✅ Importación completada:");
            $this->command->info("   📥 Insertados: {$inserted}");
            
            if ($errors > 0) {
                $this->command->warn("   ❌ Errores: {$errors}");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error fatal: " . $e->getMessage());
            Log::error("Error en importación", [
                'table' => $tableName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
        $this->command->newLine();
    }
    
    /**
     * Lee un archivo CSV y retorna array de datos
     */
    private function readCSV(string $filePath, string $delimiter = ','): array
    {
        $data = [];
        $headers = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Leer encabezados
            $headers = fgetcsv($handle, 0, $delimiter);
            
            // Limpiar BOM UTF-8
            if (isset($headers[0])) {
                $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]);
            }
            
            // Leer datos
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
     * Mapea una fila del CSV según la configuración
     */
    private function mapRow(array $row, array $config): array
    {
        $mapped = [];
        $mapping = $config['mapping'];
        $transforms = $config['transforms'] ?? [];
        
        foreach ($mapping as $csvColumn => $dbColumn) {
            if (isset($row[$csvColumn])) {
                $value = trim($row[$csvColumn]);
                
                // Aplicar transformación si existe
                if (isset($transforms[$dbColumn])) {
                    $value = $transforms[$dbColumn]($value);
                }
                
                // Convertir vacíos a null
                $mapped[$dbColumn] = $value === '' ? null : $value;
                
                // Manejar campo imagen específicamente
                if ($dbColumn === 'imagen' && ($value === '' || $value === null)) {
                    // Dejar como null, el modelo se encargará de la imagen por defecto
                    $mapped[$dbColumn] = null;
                }
            }
        }
        
        return $mapped;
    }
    
    /**
     * Resuelve relaciones foráneas (busca o crea registros)
     */
    private function resolveRelations(array $mappedData, array $originalRow, array $config): array
    {
        foreach ($config['relations'] as $dbColumn => $relation) {
            // Encontrar la columna original del CSV
            $csvColumn = array_search($dbColumn, $config['mapping']);
            
            if ($csvColumn !== false && isset($originalRow[$csvColumn])) {
                $searchValue = trim($originalRow[$csvColumn]);
                
                if (!empty($searchValue)) {
                    $id = $this->findOrCreate(
                        $relation['table'],
                        $relation['column'],
                        $searchValue
                    );
                    
                    $mappedData[$dbColumn] = $id;
                }
            }
        }
        
        return $mappedData;
    }
    
    /**
     * Busca o crea un registro relacionado
     */
    private function findOrCreate(string $table, string $column, string $value): int
    {
        $record = DB::table($table)->where($column, $value)->first();
        
        if ($record) {
            return $record->id;
        }
        
        // Crear nuevo registro
        return DB::table($table)->insertGetId([
            $column => $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
