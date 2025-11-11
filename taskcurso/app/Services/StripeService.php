<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        // Configurar la API key de Stripe
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Crear una intención de pago (PaymentIntent)
     * 
     * @param float $amount Monto en la moneda base (ej: 100.50 GTQ)
     * @param array $metadata Metadata adicional (order_id, user_id, etc.)
     * @return array
     */
    public function createPaymentIntent(float $amount, array $metadata = []): array
    {
        try {
            // Stripe maneja montos en centavos, así que multiplicamos por 100
            $amountInCents = (int) round($amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => config('stripe.currency'),
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                // Descripción del pago
                'description' => 'Pedido Sol Store - ' . ($metadata['order_number'] ?? 'N/A'),
            ]);

            Log::info('PaymentIntent creado', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'metadata' => $metadata
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Error al crear PaymentIntent', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'metadata' => $metadata
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verificar el estado de un pago
     * 
     * @param string $paymentIntentId ID del PaymentIntent
     * @return array
     */
    public function verifyPayment(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            $status = $paymentIntent->status;
            $isSuccessful = $status === 'succeeded';

            Log::info('Pago verificado', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $status,
                'amount' => $paymentIntent->amount / 100,
            ]);

            return [
                'success' => true,
                'payment_status' => $status,
                'is_paid' => $isSuccessful,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Error al verificar pago', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancelar un PaymentIntent
     * 
     * @param string $paymentIntentId
     * @return array
     */
    public function cancelPayment(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->cancel();

            Log::info('Pago cancelado', [
                'payment_intent_id' => $paymentIntentId
            ]);

            return [
                'success' => true,
                'message' => 'Pago cancelado exitosamente',
            ];

        } catch (ApiErrorException $e) {
            Log::error('Error al cancelar pago', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crear un reembolso
     * 
     * @param string $paymentIntentId
     * @param float|null $amount Monto a reembolsar (null = reembolso completo)
     * @return array
     */
    public function refundPayment(string $paymentIntentId, ?float $amount = null): array
    {
        try {
            $refundData = [
                'payment_intent' => $paymentIntentId,
            ];

            if ($amount !== null) {
                $refundData['amount'] = (int) round($amount * 100);
            }

            $refund = \Stripe\Refund::create($refundData);

            Log::info('Reembolso creado', [
                'payment_intent_id' => $paymentIntentId,
                'refund_id' => $refund->id,
                'amount' => $amount
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
                'status' => $refund->status,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Error al crear reembolso', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
