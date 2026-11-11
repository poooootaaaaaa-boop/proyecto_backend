<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planes; // <--- Importante: en plural tal como lo tienes creado
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PlanesController extends Controller
{
    // 1. Obtener todos los planes para mostrarlos en el frontend
    public function getPlanes()
    {
        try {
            $planes = Planes::all(); // <--- Usando el modelo Planes
            return response()->json($planes);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 2. Procesar el pago con Stripe
    public function pagar(Request $request)
    {
        $plan = Planes::find($request->plan_id); // <--- Usando el modelo Planes

        if (!$plan) {
            return response()->json(['error' => 'Plan no encontrado'], 404);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($plan->precio * 100), 
                'currency' => 'mxn', 
                'metadata' => [
                    'usuario_id' => $request->usuario_id,
                    'plan_id' => $plan->id,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}