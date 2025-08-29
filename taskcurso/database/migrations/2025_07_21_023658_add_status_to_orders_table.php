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
        // En este proyecto la tabla de pedidos se llama 'pedidos'
        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                if (!Schema::hasColumn('pedidos', 'status')) {
                    $table->enum('status', ['pendiente', 'en produccion', 'enviado', 'entregado'])->default('pendiente');
                }
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pedidos') && Schema::hasColumn('pedidos', 'status')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}; 
