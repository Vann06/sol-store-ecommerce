<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('atributos_producto', function (Blueprint $table) {
            $table->id();
            $table->enum('talla', ['M', 'L', 'Única']);
            $table->foreignId('id_producto')->constrained('productos');
            $table->foreignId('id_material')->constrained('materiales');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('atributos_producto');
    }
};