<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('detalle_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')->constrained('productos');
            $table->integer('stock')->default(0);
            $table->decimal('precio', 10, 2);
            $table->foreignId('created_by')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('detalle_producto');
    }
};
