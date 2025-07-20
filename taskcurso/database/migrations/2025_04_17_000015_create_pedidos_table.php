<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->dateTime('fecha_pedido')->useCurrent();
            $table->enum('estado', ['Imprimiendo', 'Pintando', 'Enviando', 'Entregado']);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('usuarios');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};

