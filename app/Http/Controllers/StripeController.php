<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Planes;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\PagoController;

class StripeController extends Controller
{
    public function crearSesion(Request $request)
    {
        $plan = Planes::find($request->plan_id);

        if (!$plan) {
            return response()->json([
                'error' => 'Plan no encontrado'
            ], 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => $plan->nombre,
                        'description' => 'Suscripción al plan ' . $plan->nombre . ' con acceso completo'
                    ],
                    'unit_amount' => $plan->precio * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',

            // IMPORTANTE: metadata también aquí
            'payment_intent_data' => [
                'description' => 'Pago de plan ' . $plan->nombre,
                'metadata' => [
                    'usuario_id' => $request->usuario_id,
                    'plan_id' => $request->plan_id
                ]
            ],

            'success_url' => 'http://localhost:3000/success',
            'cancel_url' => 'http://localhost:3000/cancel',

            // metadata de la sesión
            'metadata' => [
                'usuario_id' => $request->usuario_id,
                'plan_id' => $request->plan_id
            ]
        ]);

        return response()->json([
            'url' => $session->url
        ]);
    }

    public function webhook(Request $request)
    {
        $event = json_decode($request->getContent());

        // =========================
        // 1) PAGO EXITOSO
        // =========================
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            $usuarioId = $session->metadata->usuario_id ?? null;
            $planId = $session->metadata->plan_id ?? null;

            $plan = Planes::find($planId);

            if ($usuarioId && $plan) {
                // Registrar pago exitoso
                $pagoRequest = new Request([
                    'usuario_id'   => $usuarioId,
                    'monto'        => $plan->precio,
                    'metodo_pago'  => 'stripe',
                    'estado_pago'  => 'pagado'
                ]);

                $pagoController = new PagoController();
                $pagoController->registrarPago($pagoRequest);

                // Crear/actualizar suscripción
                $suscripcionRequest = new Request([
                    'usuario_id' => $usuarioId,
                    'plan_id'    => $planId
                ]);

                $suscripcionController = new SuscripcionController();
                $suscripcionController->AddSuscripcion($suscripcionRequest);
            }
        }

        // =========================
        // 2) PAGO FALLIDO
        // =========================
        if ($event->type == 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object;

            $usuarioId = $paymentIntent->metadata->usuario_id ?? null;
            $planId = $paymentIntent->metadata->plan_id ?? null;

            $plan = Planes::find($planId);

            if ($usuarioId && $plan) {
                $pagoRequest = new Request([
                    'usuario_id'   => $usuarioId,
                    'monto'        => $plan->precio,
                    'metodo_pago'  => 'stripe',
                    'estado_pago'  => 'cancelado'
                ]);

                $pagoController = new PagoController();
                $pagoController->registrarPago($pagoRequest);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function pagar(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $plan = Planes::find($request->plan_id);

        if (!$plan) {
            return response()->json(['error' => 'Plan no existe'], 404);
        }

        try {
            // Creamos el PaymentIntent y devolvemos el clientSecret para que el frontend lo confirme con Elements
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $plan->precio * 100,
                'currency' => 'mxn',
                'automatic_payment_methods' => ['enabled' => true],
                'description' => "Pago de plan " . $plan->nombre,
                'metadata' => [
                    'usuario_id' => $request->usuario_id,
                    'plan_id' => $request->plan_id
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);

        } catch (\Exception $e) {
            // Registrar intento fallido en caso de error al crear el intent
            $pagoFallido = new Request([
                'usuario_id'   => $request->usuario_id,
                'monto'        => $plan->precio,
                'metodo_pago'  => 'stripe',
                'estado_pago'  => 'cancelado'
            ]);

            $pagoController = new PagoController();
            $pagoController->registrarPago($pagoFallido);

            return response()->json([
                'error' => 'Pago fallido',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}