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
        Schema::table('pedidos', function (Blueprint $table) {
            // ID del PaymentIntent de Stripe
            $table->string('stripe_payment_intent_id')->nullable()->unique()->after('estado');
            
            // Estado del pago en Stripe
            $table->string('payment_status')->default('pending')->after('stripe_payment_intent_id');
            // Valores: pending, processing, succeeded, failed, canceled
            
            // Monto del pago
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_status');
            
            // Moneda del pago
            $table->string('payment_currency', 3)->default('gtq')->after('payment_amount');
            
            // Método de pago usado (card, etc.)
            $table->string('payment_method')->nullable()->after('payment_currency');
            
            // Fecha en que se completó el pago
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            
            // Índices para búsquedas rápidas
            $table->index('payment_status');
            $table->index('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['stripe_payment_intent_id']);
            
            $table->dropColumn([
                'stripe_payment_intent_id',
                'payment_status',
                'payment_amount',
                'payment_currency',
                'payment_method',
                'paid_at',
            ]);
        });
    }
};
