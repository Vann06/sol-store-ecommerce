<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_carrito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carrito')->constrained('carrito_compras')->onDelete('cascade');
            $table->foreignId('id_detalle_producto')->constrained('detalle_producto')->onDelete('cascade');
            $table->integer('cantidad')->check('cantidad > 0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_carrito');
    }
};
