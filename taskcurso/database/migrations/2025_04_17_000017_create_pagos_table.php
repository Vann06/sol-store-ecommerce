<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pedido')->constrained('pedidos')->unique();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['visa', 'mastercard', 'american_express']);
            $table->enum('estado', ['pendiente', 'completado', 'fallido']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};