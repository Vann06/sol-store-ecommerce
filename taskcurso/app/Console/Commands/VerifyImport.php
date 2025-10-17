<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyImport extends Command
{
    protected $signature = 'import:verify';
    protected $description = 'Verifica que los datos se hayan importado correctamente';

    public function handle()
    {
        $this->info("🔍 Verificando datos importados...");
        $this->newLine();
        
        $tables = [
            'categories' => [
                'name' => 'Categorías',
                'expected' => 7,
                'key_fields' => ['name'],
            ],
            'themes' => [
                'name' => 'Temáticas',
                'expected' => 8,
                'key_fields' => ['name'],
            ],
            'materiales' => [
                'name' => 'Materiales',
                'expected' => 10,
                'key_fields' => ['nombre'],
            ],
            'usuarios' => [
                'name' => 'Usuarios',
                'expected' => 5,
                'key_fields' => ['first_name', 'last_name', 'email'],
            ],
            'productos' => [
                'name' => 'Productos',
                'expected' => 20,
                'key_fields' => ['nombre', 'precio_base', 'stock'],
            ],
        ];
        
        $allOk = true;
        
        foreach ($tables as $table => $config) {
            $this->info("📊 {$config['name']} ({$table})");
            
            try {
                $count = DB::table($table)->count();
                
                if ($count === 0) {
                    $this->warn("   ⚠️  Vacía (0 registros)");
                    $this->warn("   💡 Importa primero: php artisan db:seed --class=ExcelImportSeeder");
                    $allOk = false;
                } else {
                    $expected = $config['expected'];
                    $status = $count >= $expected ? '✅' : '⚠️';
                    $this->line("   {$status} Total: {$count} registros (esperados: {$expected})");
                    
                    // Mostrar ejemplos
                    $samples = DB::table($table)->limit(3)->get($config['key_fields']);
                    
                    if ($samples->isNotEmpty()) {
                        $this->line("   📝 Ejemplos:");
                        foreach ($samples as $sample) {
                            $values = array_values((array) $sample);
                            $this->line("      - " . implode(' | ', $values));
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
                $allOk = false;
            }
            
            $this->newLine();
        }
        
        // Verificar integridad referencial
        $this->info("🔗 Verificando integridad referencial...");
        $this->newLine();
        
        $this->checkRelations();
        
        $this->newLine();
        
        if ($allOk) {
            $this->info("✅ Todos los datos fueron importados correctamente!");
        } else {
            $this->warn("⚠️  Algunas tablas necesitan atención.");
        }
        
        return Command::SUCCESS;
    }
    
    private function checkRelations()
    {
        try {
            // Verificar productos con categorías válidas
            $productosCount = DB::table('productos')->count();
            $productosWithCat = DB::table('productos')
                ->join('categories', 'productos.id_categoria', '=', 'categories.id')
                ->count();
            
            if ($productosCount === $productosWithCat) {
                $this->info("   ✅ Productos → Categorías: OK ({$productosWithCat}/{$productosCount})");
            } else {
                $this->warn("   ⚠️  Productos → Categorías: {$productosWithCat}/{$productosCount}");
            }
            
            // Verificar productos con temáticas válidas
            $productosWithTheme = DB::table('productos')
                ->join('themes', 'productos.id_tematica', '=', 'themes.id')
                ->count();
            
            if ($productosCount === $productosWithTheme) {
                $this->info("   ✅ Productos → Temáticas: OK ({$productosWithTheme}/{$productosCount})");
            } else {
                $this->warn("   ⚠️  Productos → Temáticas: {$productosWithTheme}/{$productosCount}");
            }
            
            // Mostrar productos huérfanos si existen
            $orphans = DB::table('productos as p')
                ->leftJoin('categories as c', 'p.id_categoria', '=', 'c.id')
                ->leftJoin('themes as t', 'p.id_tematica', '=', 't.id')
                ->whereNull('c.id')
                ->orWhereNull('t.id')
                ->select('p.nombre')
                ->get();
            
            if ($orphans->isNotEmpty()) {
                $this->warn("   ⚠️  Productos con relaciones inválidas:");
                foreach ($orphans as $orphan) {
                    $this->warn("      - {$orphan->nombre}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error verificando relaciones: " . $e->getMessage());
        }
    }
}
