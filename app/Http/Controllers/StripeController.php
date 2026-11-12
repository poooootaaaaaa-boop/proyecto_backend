<?php

namespace App\Http\Controllers;

use App\Models\Planes;
use App\Models\Suscripciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeController extends Controller
{
    /**
     * Checkout hospedado por Stripe. El alta de la suscripción ocurre en el webhook.
     */
    public function crearSesion(Request $request)
    {
        $datos = $this->validarSolicitudPago($request);
        $plan = $datos['plan'];

        if ($rechazo = $this->rechazarSiPlanYaActivo($datos['usuario_id'], $plan->id)) {
            return $rechazo;
        }

        $this->configurarStripe();

        $metadata = [
            'usuario_id' => (string) $datos['usuario_id'],
            'plan_id' => (string) $plan->id,
            'origen' => 'checkout',
        ];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => $plan->nombre,
                        'description' => 'Suscripción al plan ' . $plan->nombre . ' con acceso completo',
                    ],
                    'unit_amount' => $this->montoEnCentavos($plan),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'payment_intent_data' => [
                'description' => 'Pago de plan ' . $plan->nombre,
                'metadata' => $metadata,
            ],
            'success_url' => config('services.stripe.success_url'),
            'cancel_url' => config('services.stripe.cancel_url'),
            'metadata' => $metadata,
        ]);

        return response()->json([
            'url' => $session->url,
        ]);
    }

    /**
     * PaymentIntent para Stripe Elements. El alta de la suscripción ocurre en el webhook.
     */
    public function pagar(Request $request)
    {
        $datos = $this->validarSolicitudPago($request);
        $plan = $datos['plan'];

        if ($rechazo = $this->rechazarSiPlanYaActivo($datos['usuario_id'], $plan->id)) {
            return $rechazo;
        }

        $this->configurarStripe();

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->montoEnCentavos($plan),
                'currency' => 'mxn',
                'automatic_payment_methods' => ['enabled' => true],
                'description' => 'Pago de plan ' . $plan->nombre,
                'metadata' => [
                    'usuario_id' => (string) $datos['usuario_id'],
                    'plan_id' => (string) $plan->id,
                    'origen' => 'elements',
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('No se pudo crear el PaymentIntent de Stripe', [
                'usuario_id' => $datos['usuario_id'],
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            $this->registrarPago($datos['usuario_id'], $plan->precio, 'cancelado');

            return response()->json([
                'error' => 'Pago fallido',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            Log::error('STRIPE_WEBHOOK_SECRET no está configurado');

            return response()->json(['error' => 'Webhook no configurado'], 500);
        }

        $firma = $request->header('Stripe-Signature');

        if (!$firma) {
            return response()->json(['error' => 'Firma ausente'], 400);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $firma,
                $secret
            );
        } catch (UnexpectedValueException $e) {
            return response()->json(['error' => 'Payload inválido'], 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Firma de webhook de Stripe rechazada', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Firma inválida'], 400);
        }

        $claveEvento = 'stripe_event_' . $event->id;

        try {
            if (Cache::has($claveEvento)) {
                return response()->json(['ok' => true]);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo consultar la idempotencia del webhook de Stripe', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            match ($event->type) {
                'checkout.session.completed' => $this->procesarCheckoutCompletado($event->data->object),
                'payment_intent.succeeded' => $this->procesarPaymentIntentExitoso($event->data->object),
                'payment_intent.payment_failed' => $this->procesarPagoFallido($event->data->object),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Error al procesar el webhook de Stripe', [
                'event_id' => $event->id,
                'type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al procesar el evento'], 500);
        }

        try {
            Cache::put($claveEvento, true, now()->addDays(3));
        } catch (\Throwable $e) {
            Log::warning('No se pudo guardar la idempotencia del webhook de Stripe', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    private function procesarCheckoutCompletado(object $session): void
    {
        if (($session->payment_status ?? null) !== 'paid') {
            return;
        }

        $this->confirmarPagoYSuscripcion($session->metadata ?? null);
    }

    private function procesarPaymentIntentExitoso(object $paymentIntent): void
    {
        $metadata = $paymentIntent->metadata ?? null;

        // Checkout también emite payment_intent.succeeded. Ese caso ya se
        // confirma en checkout.session.completed para no duplicar el cobro.
        if ($this->metadata($metadata, 'origen') !== 'elements') {
            return;
        }

        $this->confirmarPagoYSuscripcion($metadata);
    }

    private function procesarPagoFallido(object $paymentIntent): void
    {
        $usuarioId = $this->metadata($paymentIntent->metadata ?? null, 'usuario_id');
        $planId = $this->metadata($paymentIntent->metadata ?? null, 'plan_id');
        $plan = $planId ? Planes::find($planId) : null;

        if (!$usuarioId || !$plan) {
            return;
        }

        $this->registrarPago($usuarioId, $plan->precio, 'cancelado');
    }

    private function confirmarPagoYSuscripcion(mixed $metadata): void
    {
        $usuarioId = $this->metadata($metadata, 'usuario_id');
        $planId = $this->metadata($metadata, 'plan_id');
        $plan = $planId ? Planes::find($planId) : null;

        if (!$usuarioId || !$plan) {
            Log::warning('Webhook de Stripe sin usuario o plan válido', [
                'usuario_id' => $usuarioId,
                'plan_id' => $planId,
            ]);

            return;
        }

        // La suscripción se actualiza primero: AddSuscripcion reescribe la misma
        // fila del usuario, así un reintento de Stripe no crea suscripciones duplicadas.
        $this->activarSuscripcion($usuarioId, $plan->id);
        $this->registrarPago($usuarioId, $plan->precio, 'pagado');
    }

    private function activarSuscripcion(string $usuarioId, int|string $planId): void
    {
        $solicitud = Request::create('/interno/suscripcion', 'POST', [
            'usuario_id' => $usuarioId,
            'plan_id' => $planId,
        ]);

        $respuesta = app(SuscripcionController::class)->AddSuscripcion($solicitud);

        if ($respuesta->getStatusCode() >= 400) {
            throw new \RuntimeException(
                'No se pudo actualizar la suscripción: ' . $respuesta->getContent()
            );
        }
    }

    private function registrarPago(string|int $usuarioId, mixed $monto, string $estado): void
    {
        $solicitud = Request::create('/interno/pago', 'POST', [
            'usuario_id' => $usuarioId,
            'monto' => $monto,
            'metodo_pago' => 'stripe',
            'estado_pago' => $estado,
        ]);

        $respuesta = app(PagoController::class)->registrarPago($solicitud);

        if ($respuesta->getStatusCode() >= 400) {
            throw new \RuntimeException(
                'No se pudo registrar el pago: ' . $respuesta->getContent()
            );
        }
    }

    private function rechazarSiPlanYaActivo(int $usuarioId, int $planId)
    {
        $yaActivo = Suscripciones::query()
            ->where('usuario_id', $usuarioId)
            ->where('plan_id', $planId)
            ->where('estado', 'activo')
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', now()->toDateString());
            })
            ->exists();

        if (!$yaActivo) {
            return null;
        }

        return response()->json([
            'error' => 'Ya cuentas con este plan activo actualmente',
        ], 400);
    }

    private function validarSolicitudPago(Request $request): array
    {
        $validated = $request->validate([
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'plan_id' => 'required|integer|exists:planes,id',
        ]);

        return [
            'usuario_id' => $validated['usuario_id'],
            'plan' => Planes::findOrFail($validated['plan_id']),
        ];
    }

    private function configurarStripe(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    private function montoEnCentavos(Planes $plan): int
    {
        return (int) round(((float) $plan->precio) * 100);
    }

    private function metadata(mixed $metadata, string $key): ?string
    {
        if (is_array($metadata)) {
            $value = $metadata[$key] ?? null;
        } elseif (is_object($metadata)) {
            $value = $metadata->{$key} ?? null;
        } else {
            return null;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
