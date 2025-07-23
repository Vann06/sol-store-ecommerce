<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carrito_compras', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->nullable(); // Nullable para usuarios invitados
            $table->string('session_id')->nullable(); // Para usuarios no autenticados
            $table->timestamps();
            
            // Índices
            $table->index(['id_usuario', 'session_id']);
            
            // Foreign key
            $table->foreign('id_usuario')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('carrito_compras');
    }
};