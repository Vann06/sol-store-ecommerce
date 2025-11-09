<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index para acelerar filtros por estado en pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'estado')) {
                return; // seguridad por si el esquema difiere
            }
            $table->index('estado', 'idx_pedidos_estado');
            // created_at es usado en filtros de fecha
            $table->index('created_at', 'idx_pedidos_created_at');
        });

        // Index para acelerar agrupaciones/filtrados por fecha en historial_ventas
        Schema::table('historial_ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('historial_ventas', 'fecha_venta')) {
                return;
            }
            $table->index('fecha_venta', 'idx_historial_ventas_fecha');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Drop indexes si existen
            try { $table->dropIndex('idx_pedidos_estado'); } catch (\Throwable $e) {}
            try { $table->dropIndex('idx_pedidos_created_at'); } catch (\Throwable $e) {}
        });

        Schema::table('historial_ventas', function (Blueprint $table) {
            try { $table->dropIndex('idx_historial_ventas_fecha'); } catch (\Throwable $e) {}
        });
    }
};
