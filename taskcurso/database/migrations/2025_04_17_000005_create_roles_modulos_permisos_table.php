<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('roles_modulos_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rol')->constrained('roles');
            $table->foreignId('id_modulo')->constrained('modulos');
            $table->foreignId('id_permiso')->constrained('permisos');
            $table->timestamps(); // para trazabilidad
        });
    }

    public function down(): void {
        Schema::dropIfExists('roles_modulos_permisos');
    }
};