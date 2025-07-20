<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 2000);
            $table->foreignId('id_categoria')->constrained('categories');
            $table->foreignId('id_tematica')->constrained('themes');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_base', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('imagen', 2000)->nullable();
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('usuarios');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios');
            $table->softDeletes(); // agrega deleted_at automáticamente
            $table->foreignId('deleted_by')->nullable()->constrained('usuarios');
        });
        
               
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
