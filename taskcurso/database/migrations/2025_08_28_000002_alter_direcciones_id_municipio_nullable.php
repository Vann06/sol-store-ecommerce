<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            // En algunos drivers hay que soltar primero la FK
            try { $table->dropForeign(['id_municipio']); } catch (\Throwable $e) {}
        });

        Schema::table('direcciones', function (Blueprint $table) {
            // Hacer la columna nullable
            $table->bigInteger('id_municipio')->nullable()->change();
            // Volver a crear la FK
            $table->foreign('id_municipio')->references('id')->on('municipios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            try { $table->dropForeign(['id_municipio']); } catch (\Throwable $e) {}
        });

        Schema::table('direcciones', function (Blueprint $table) {
            $table->bigInteger('id_municipio')->nullable(false)->change();
            $table->foreign('id_municipio')->references('id')->on('municipios')->onDelete('cascade');
        });
    }
};
