<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->foreignId('id_rol')->constrained('roles');
            $table->timestamps(); // para trazabilidad
        });
    }

    public function down(): void {
        Schema::dropIfExists('usuarios_roles');
    }
};