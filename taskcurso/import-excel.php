<?php

/**
 * Script para importar datos desde Excel (CSV) a la base de datos
 * 
 * USO:
 * 1. Exporta tu Excel a CSV (separado por comas)
 * 2. Coloca el archivo CSV en la carpeta 'storage/app/'
 * 3. Ejecuta: php import-excel.php
 * 
 * REQUISITOS:
 * - El archivo CSV debe tener encabezados en la primera fila
 * - Ajusta la configuración según tu tabla objetivo
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// ============ CONFIGURACIÓN ============
$csvFile = 'storage/app/productos.csv'; // Cambia esto por tu archivo
$tableName = 'productos'; // Nombre de la tabla a poblar
$batchSize = 100; // Número de registros por lote

// Mapeo de columnas: CSV => Base de datos
$columnMapping = [
    'Nombre' => 'nombre',
    'Descripcion' => 'descripcion',
    'Precio' => 'precio',
    'Stock' => 'stock',
    'Categoria' => 'id_categoria',
    // Agrega más mapeos según necesites
];

// ============ FUNCIONES AUXILIARES ============

/**
 * Lee un archivo CSV y retorna un array de datos
 */
function readCSV($filePath, $delimiter = ',', $encoding = 'UTF-8') {
    if (!file_exists($filePath)) {
        die("❌ Error: El archivo '$filePath' no existe.\n");
    }

    $data = [];
    $headers = [];
    
    if (($handle = fopen($filePath, 'r')) !== false) {
        // Detectar encoding
        $firstLine = fgets($handle);
        rewind($handle);
        
        if (mb_detect_encoding($firstLine, 'UTF-8', true) === false) {
            // Si no es UTF-8, intentar convertir desde ISO-8859-1 o Windows-1252
            stream_filter_append($handle, 'convert.iconv.WINDOWS-1252/UTF-8');
        }
        
        // Leer encabezados
        $headers = fgetcsv($handle, 0, $delimiter);
        
        // Limpiar BOM si existe
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
    
    return ['headers' => $headers, 'data' => $data];
}

/**
 * Mapea los datos del CSV según la configuración
 */
function mapData($row, $mapping) {
    $mapped = [];
    
    foreach ($mapping as $csvColumn => $dbColumn) {
        if (isset($row[$csvColumn])) {
            $value = trim($row[$csvColumn]);
            
            // Convertir valores vacíos a null
            $mapped[$dbColumn] = $value === '' ? null : $value;
        }
    }
    
    // Agregar timestamps
    $mapped['created_at'] = now();
    $mapped['updated_at'] = now();
    
    return $mapped;
}

/**
 * Busca o crea un registro relacionado (ejemplo: categoría)
 */
function findOrCreateCategory($nombre) {
    if (empty($nombre)) {
        return null;
    }
    
    $category = DB::table('categories')->where('nombre', $nombre)->first();
    
    if (!$category) {
        $id = DB::table('categories')->insertGetId([
            'nombre' => $nombre,
            'descripcion' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $id;
    }
    
    return $category->id;
}

// ============ PROCESO PRINCIPAL ============

try {
    echo "📊 Iniciando importación desde Excel/CSV...\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Leer archivo CSV
    echo "📖 Leyendo archivo: $csvFile\n";
    $csvData = readCSV($csvFile);
    $totalRows = count($csvData['data']);
    
    echo "✅ Archivo leído correctamente\n";
    echo "   Columnas encontradas: " . implode(', ', $csvData['headers']) . "\n";
    echo "   Total de filas: $totalRows\n\n";
    
    if ($totalRows === 0) {
        die("⚠️  El archivo está vacío.\n");
    }
    
    // Confirmar antes de proceder
    echo "⚠️  Se insertarán $totalRows registros en la tabla '$tableName'\n";
    echo "¿Deseas continuar? (s/n): ";
    $confirm = trim(fgets(STDIN));
    
    if (strtolower($confirm) !== 's') {
        die("❌ Operación cancelada.\n");
    }
    
    echo "\n🚀 Iniciando inserción...\n";
    
    // Iniciar transacción
    DB::beginTransaction();
    
    $inserted = 0;
    $errors = 0;
    $batch = [];
    
    foreach ($csvData['data'] as $index => $row) {
        try {
            // Mapear datos
            $mappedData = mapData($row, $columnMapping);
            
            // Procesar relaciones (ejemplo: buscar categoría)
            if (isset($row['Categoria'])) {
                $mappedData['id_categoria'] = findOrCreateCategory($row['Categoria']);
            }
            
            $batch[] = $mappedData;
            
            // Insertar por lotes
            if (count($batch) >= $batchSize) {
                DB::table($tableName)->insert($batch);
                $inserted += count($batch);
                $batch = [];
                
                $progress = round(($inserted / $totalRows) * 100, 2);
                echo "   Progreso: $inserted/$totalRows ($progress%)\r";
            }
            
        } catch (\Exception $e) {
            $errors++;
            echo "\n⚠️  Error en fila " . ($index + 2) . ": " . $e->getMessage() . "\n";
        }
    }
    
    // Insertar registros restantes
    if (count($batch) > 0) {
        DB::table($tableName)->insert($batch);
        $inserted += count($batch);
    }
    
    // Confirmar transacción
    DB::commit();
    
    echo "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Importación completada!\n";
    echo "   📥 Registros insertados: $inserted\n";
    echo "   ❌ Errores: $errors\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error fatal: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
