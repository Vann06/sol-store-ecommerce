<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Solo para PostgreSQL: el enum original era un CHECK constraint
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");

        if ($driver === 'pgsql') {
            // 1. Eliminar constraint existente (nombre visto en error: pedidos_estado_check)
            DB::statement('ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_estado_check');

            // 2. Mapear valores antiguos a nuevos
            // Imprimiendo -> Procesando
            // Pintando    -> Procesando
            // Enviando    -> Enviado
            // Entregado   -> Entregado (igual)
            DB::statement("UPDATE pedidos SET estado = 'Procesando' WHERE estado IN ('Imprimiendo','Pintando')");
            DB::statement("UPDATE pedidos SET estado = 'Enviado' WHERE estado = 'Enviando'");
            // Entregado se mantiene

            // 3. Añadir nuevo constraint con los valores requeridos
            DB::statement("ALTER TABLE pedidos ADD CONSTRAINT pedidos_estado_check CHECK (estado IN ('Procesando','Enviado','Entregado','Cancelado'))");
        } else {
            // Para MySQL u otros: alterar columna a VARCHAR y no usar ENUM rígido (opcional)
            Schema::table('pedidos', function ($table) {
                // Laravel no puede convertir enum fácilmente de forma portable aquí sin Blueprint extendido.
                // Se asume que la columna será tratada como string si no es pgsql.
            });
        }
    }

    public function down(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_estado_check');
            // Restaurar constraint original
            DB::statement("ALTER TABLE pedidos ADD CONSTRAINT pedidos_estado_check CHECK (estado IN ('Imprimiendo','Pintando','Enviando','Entregado'))");
            // No revertimos los valores ya mapeados porque podría perder consistencia; opcional agregar aquí.
        }
    }
};
