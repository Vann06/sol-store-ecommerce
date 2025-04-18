<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pedido')->unique()->constrained('pedidos')->onDelete('cascade');
            $table->dateTime('fecha_venta')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('monto_total', 10, 2)->check('monto_total > 0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_ventas');
    }
};
