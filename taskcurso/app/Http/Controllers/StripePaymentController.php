<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StripePaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Crear una intención de pago para un pedido
     * 
     * POST /api/payments/create-intent
     * Body: { order_id: 1, amount: 150.50 }
     */
    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:pedidos,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pedido = Pedido::findOrFail($request->order_id);

            // Verificar que el pedido no esté ya pagado
            if ($pedido->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido ya ha sido pagado'
                ], 400);
            }

            // Generar número de orden único
            $orderNumber = 'ORD-' . str_pad($pedido->id, 6, '0', STR_PAD_LEFT);

            // Crear PaymentIntent en Stripe
            $result = $this->stripeService->createPaymentIntent(
                $request->amount,
                [
                    'order_id' => $pedido->id,
                    'order_number' => $orderNumber,
                    'user_id' => $pedido->id_usuario,
                ]
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la intención de pago',
                    'error' => $result['error']
                ], 500);
            }

            // Guardar el PaymentIntent ID en el pedido
            $pedido->update([
                'stripe_payment_intent_id' => $result['payment_intent_id'],
                'payment_status' => 'pending',
                'payment_amount' => $request->amount,
                'payment_currency' => config('stripe.currency'),
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $result['client_secret'],
                'payment_intent_id' => $result['payment_intent_id'],
                'publishable_key' => config('stripe.key'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear PaymentIntent', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar el estado de un pago y confirmar el pedido
     * 
     * POST /api/payments/verify
     * Body: { payment_intent_id: "pi_xxx" }
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Verificar el pago en Stripe
            $result = $this->stripeService->verifyPayment($request->payment_intent_id);

            if (!$result['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error al verificar el pago',
                    'error' => $result['error']
                ], 500);
            }

            // Buscar el pedido asociado
            $pedido = Pedido::where('stripe_payment_intent_id', $request->payment_intent_id)->first();

            if (!$pedido) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un pedido asociado a este pago'
                ], 404);
            }

            // ✅ VALIDACIÓN ADICIONAL: Verificar que el pedido no esté ya pagado
            if ($pedido->isPaid()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido ya ha sido pagado anteriormente',
                    'order_id' => $pedido->id,
                    'payment_status' => 'succeeded',
                ], 400);
            }
            
            // ✅ VALIDACIÓN: El payment_status debe ser 'succeeded'
            if ($result['payment_status'] !== 'succeeded') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El pago no tiene estado de éxito en Stripe',
                    'payment_status' => $result['payment_status'],
                ], 400);
            }

            // Actualizar el estado del pago en el pedido
            if ($result['is_paid']) {
                // ✅ PAGO EXITOSO VERIFICADO EN STRIPE
                try {
                    $pedido->markAsPaid(
                        $request->payment_intent_id,
                        $result['payment_method'] ?? 'card'
                    );
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al marcar pedido como pagado', [
                        'error' => $e->getMessage(),
                        'order_id' => $pedido->id,
                        'payment_intent_id' => $request->payment_intent_id
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 400);
                }

                // ✅ VACIAR EL CARRITO DESPUÉS DE CONFIRMAR EL PAGO
                try {
                    // Usar la relación 'user' que está correctamente configurada
                    $user = $pedido->user;
                    if ($user) {
                        $carrito = \App\Models\CarritoCompra::obtenerCarrito($user->id, null);
                        if ($carrito) {
                            \App\Models\DetalleCarrito::where('id_carrito', $carrito->id)->delete();
                            Log::info('Carrito vaciado después de pago exitoso', [
                                'user_id' => $user->id,
                                'order_id' => $pedido->id,
                                'carrito_id' => $carrito->id
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // No hacer rollback si falla el vaciado del carrito
                    // El pago ya se procesó correctamente
                    Log::warning('Error al vaciar carrito después del pago', [
                        'error' => $e->getMessage(),
                        'order_id' => $pedido->id
                    ]);
                }

                // Aquí puedes agregar lógica adicional:
                // - Actualizar inventario
                // - Enviar email de confirmación
                // - Crear registro en historial de ventas
                // - etc.

                DB::commit();

                Log::info('Pago verificado exitosamente', [
                    'order_id' => $pedido->id,
                    'payment_intent_id' => $request->payment_intent_id,
                    'amount' => $pedido->payment_amount,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pago verificado y pedido confirmado exitosamente',
                    'order_id' => $pedido->id,
                    'payment_status' => $result['payment_status'],
                    'paid_at' => $pedido->paid_at,
                ]);

            } else {
                // ❌ PAGO NO EXITOSO
                $pedido->update([
                    'payment_status' => $result['payment_status'],
                ]);

                DB::rollBack();

                Log::warning('Intento de verificar pago no exitoso', [
                    'order_id' => $pedido->id,
                    'payment_intent_id' => $request->payment_intent_id,
                    'payment_status' => $result['payment_status'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'El pago no se completó exitosamente',
                    'payment_status' => $result['payment_status'],
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al verificar pago', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $request->payment_intent_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para recibir eventos de Stripe
     * 
     * POST /api/payments/webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            // Verificar la firma del webhook
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            Log::info('Webhook de Stripe recibido', [
                'type' => $event->type,
                'id' => $event->id
            ]);

            // Manejar diferentes tipos de eventos
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSuccess($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailure($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $this->handlePaymentCanceled($event->data->object);
                    break;

                default:
                    Log::info('Evento de webhook no manejado: ' . $event->type);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error al procesar webhook de Stripe', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false], 400);
        }
    }

    /**
     * Manejar pago exitoso desde webhook
     */
    protected function handlePaymentSuccess($paymentIntent)
    {
        $pedido = Pedido::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($pedido && !$pedido->isPaid()) {
            $pedido->markAsPaid(
                $paymentIntent->id,
                $paymentIntent->payment_method ?? 'card'
            );

            // ✅ VACIAR EL CARRITO después de confirmar el pago desde webhook
            try {
                // Usar la relación 'user' que está correctamente configurada
                $user = $pedido->user;
                if ($user) {
                    $carrito = \App\Models\CarritoCompra::obtenerCarrito($user->id, null);
                    if ($carrito) {
                        \App\Models\DetalleCarrito::where('id_carrito', $carrito->id)->delete();
                        Log::info('Carrito vaciado después de webhook de pago exitoso', [
                            'user_id' => $user->id,
                            'order_id' => $pedido->id,
                            'carrito_id' => $carrito->id
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error al vaciar carrito después del webhook', [
                    'error' => $e->getMessage(),
                    'order_id' => $pedido->id
                ]);
            }

            Log::info('Pedido marcado como pagado desde webhook', [
                'order_id' => $pedido->id,
                'payment_intent_id' => $paymentIntent->id
            ]);

            // Aquí puedes agregar más lógica:
            // - Enviar email de confirmación
            // - Notificar al usuario
            // - etc.
        }
    }

    /**
     * Manejar pago fallido desde webhook
     */
    protected function handlePaymentFailure($paymentIntent)
    {
        $pedido = Pedido::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($pedido) {
            $pedido->markAsPaymentFailed();

            Log::warning('Pago fallido para pedido', [
                'order_id' => $pedido->id,
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
    }

    /**
     * Manejar pago cancelado desde webhook
     */
    protected function handlePaymentCanceled($paymentIntent)
    {
        $pedido = Pedido::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($pedido) {
            $pedido->update(['payment_status' => 'canceled']);

            Log::info('Pago cancelado para pedido', [
                'order_id' => $pedido->id,
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
    }

    /**
     * Cancelar un pago
     * 
     * POST /api/payments/cancel
     * Body: { payment_intent_id: "pi_xxx" }
     */
    public function cancelPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->stripeService->cancelPayment($request->payment_intent_id);

            if ($result['success']) {
                // Actualizar el pedido
                $pedido = Pedido::where('stripe_payment_intent_id', $request->payment_intent_id)->first();
                if ($pedido) {
                    $pedido->update(['payment_status' => 'canceled']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Pago cancelado exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el pago',
                'error' => $result['error']
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
