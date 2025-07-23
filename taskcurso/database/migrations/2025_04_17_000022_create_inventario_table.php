<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_detalle_producto')->unique()->constrained('detalle_producto')->onDelete('cascade');
            $table->integer('stock_actual')->check('stock_actual >= 0');
            $table->integer('cantidad_en_produccion')->check('cantidad_en_produccion >= 0');
            $table->dateTime('fecha_actualizacion')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
