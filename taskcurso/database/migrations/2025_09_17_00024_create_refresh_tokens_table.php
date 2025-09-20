<?php
// filepath: database/migrations/xxxx_xx_xx_create_refresh_tokens_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 500)->index();
            $table->timestamp('expires_at')->index();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('refresh_tokens');
    }
};