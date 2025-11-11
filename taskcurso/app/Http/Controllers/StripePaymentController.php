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
     * Crear una intención de pago SIN crear el pedido aún
     * El pedido solo se creará después de confirmar el pago
     * 
     * POST /api/payments/create-intent
     * Body: { amount: 150.50, direccion_id: 2 }
     */
    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'direccion_id' => 'required|exists:direcciones,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Verificar que la dirección pertenezca al usuario
            $direccion = \App\Models\Direccion::where('id', $request->direccion_id)
                ->where('id_usuario', $user->id)
                ->first();
            
            if (!$direccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dirección no válida'
                ], 403);
            }

            // Crear PaymentIntent en Stripe con metadata del carrito
            $result = $this->stripeService->createPaymentIntent(
                $request->amount,
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'direccion_id' => $request->direccion_id,
                    'description' => 'Pedido pendiente de confirmación',
                ]
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la intención de pago',
                    'error' => $result['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'client_secret' => $result['client_secret'],
                'payment_intent_id' => $result['payment_intent_id'],
                'publishable_key' => config('stripe.key'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear PaymentIntent', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar el estado de un pago y CREAR el pedido
     * Solo se crea el pedido después de confirmar el pago exitoso
     * 
     * POST /api/payments/verify
     * Body: { payment_intent_id: "pi_xxx", direccion_id: 2 }
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'direccion_id' => 'required|exists:direcciones,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = $request->user();
            
            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

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

            // ✅ VALIDACIÓN: El payment_status debe ser 'succeeded'
            if ($result['payment_status'] !== 'succeeded' || !$result['is_paid']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El pago no se completó exitosamente en Stripe',
                    'payment_status' => $result['payment_status'],
                ], 400);
            }

            // Verificar si ya existe un pedido con este payment_intent_id
            $pedidoExistente = Pedido::where('stripe_payment_intent_id', $request->payment_intent_id)->first();
            
            if ($pedidoExistente) {
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Este pedido ya fue procesado anteriormente',
                    'order_id' => $pedidoExistente->id,
                    'payment_status' => 'succeeded',
                    'paid_at' => $pedidoExistente->paid_at,
                ]);
            }

            // ✅ CREAR EL PEDIDO AHORA QUE EL PAGO FUE CONFIRMADO
            $pedidoController = new \App\Http\Controllers\PedidoController();
            $checkoutRequest = new \Illuminate\Http\Request([
                'direccion_id' => $request->direccion_id
            ]);
            $checkoutRequest->setUserResolver(function () use ($user) {
                return $user;
            });

            $checkoutResponse = $pedidoController->checkout($checkoutRequest);
            $checkoutData = $checkoutResponse->getData(true);

            if (!isset($checkoutData['pedido'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el pedido',
                    'error' => $checkoutData['error'] ?? 'Error desconocido'
                ], 500);
            }

            $pedido = Pedido::find($checkoutData['pedido']['id']);

            if (!$pedido) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo recuperar el pedido creado'
                ], 500);
            }

            // ✅ MARCAR EL PEDIDO COMO PAGADO
            try {
                $pedido->markAsPaid(
                    $request->payment_intent_id,
                    $result['payment_method'] ?? 'card'
                );
                
                // Actualizar el monto del pago
                // NOTA: $result['amount'] YA viene en la moneda correcta (no en centavos)
                // porque StripeService->verifyPayment() ya hace la conversión
                $pedido->update([
                    'payment_amount' => $result['amount'], // Ya convertido de centavos
                    'payment_currency' => strtolower($result['currency'] ?? 'gtq'),
                ]);

                // ✅ CREAR REGISTRO EN HISTORIAL DE VENTAS para los reportes
                \App\Models\HistorialVenta::create([
                    'id_pedido' => $pedido->id,
                    'fecha_venta' => now(),
                    'monto_total' => $result['amount'], // Ya convertido de centavos
                ]);

                Log::info('Registro de venta creado en historial', [
                    'order_id' => $pedido->id,
                    'amount' => $result['amount']
                ]);
                
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
                $carrito = \App\Models\CarritoCompra::obtenerCarrito($user->id, null);
                if ($carrito) {
                    \App\Models\DetalleCarrito::where('id_carrito', $carrito->id)->delete();
                    Log::info('Carrito vaciado después de pago exitoso', [
                        'user_id' => $user->id,
                        'order_id' => $pedido->id,
                        'carrito_id' => $carrito->id
                    ]);
                }
            } catch (\Exception $e) {
                // No hacer rollback si falla el vaciado del carrito
                // El pago ya se procesó correctamente
                Log::warning('Error al vaciar carrito después del pago', [
                    'error' => $e->getMessage(),
                    'order_id' => $pedido->id
                ]);
            }

            DB::commit();

            Log::info('Pago verificado exitosamente y pedido creado', [
                'order_id' => $pedido->id,
                'payment_intent_id' => $request->payment_intent_id,
                'amount' => $pedido->payment_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago verificado y pedido creado exitosamente',
                'order_id' => $pedido->id,
                'payment_status' => $result['payment_status'],
                'paid_at' => $pedido->paid_at,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al verificar pago y crear pedido', [
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
