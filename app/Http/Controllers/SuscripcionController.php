<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripciones;
use App\Models\Planes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Historialsuscripciones;

class SuscripcionController extends Controller
{
    public function activa(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|integer|exists:usuarios,id',
        ]);

        $suscripcion = Suscripciones::query()
            ->with('plan')
            ->where('usuario_id', $data['usuario_id'])
            ->where('estado', 'activo')
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', now()->toDateString());
            })
            ->orderByDesc('id')
            ->first();

        if (!$suscripcion || !$suscripcion->plan) {
            return response()->json([
                'plan_id' => null,
                'es_pago' => false,
            ]);
        }

        $nombre = strtolower((string) $suscripcion->plan->nombre);
        $slug = strtolower((string) ($suscripcion->plan->slug ?? ''));
        $esGratuito = (float) $suscripcion->plan->precio <= 0
            || str_contains($nombre, 'freem')
            || str_contains($slug, 'freem');

        return response()->json([
            'plan_id' => $suscripcion->plan_id,
            'es_pago' => !$esGratuito,
        ]);
    }

    public function AddSuscripcion(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'plan_id' => 'required|integer|exists:planes,id',
        ]);

        $plan = Planes::findOrFail($data['plan_id']);

        $suscripcion = DB::transaction(function () use ($data, $plan) {
            $anteriores = Suscripciones::query()
                ->where('usuario_id', $data['usuario_id'])
                ->lockForUpdate()
                ->orderByDesc('id')
                ->get();

            $planAnterior = $anteriores
                ->firstWhere('estado', 'activo')
                ?->plan_id;

            $hoy = now()->startOfDay();

            foreach ($anteriores as $anterior) {
                if ($anterior->estado !== 'activo') {
                    continue;
                }

                $yaVencio = $anterior->fecha_fin
                    && Carbon::parse($anterior->fecha_fin)->startOfDay()->lt($hoy);

                $anterior->estado = $yaVencio ? 'vencido' : 'inactivo';
                $anterior->save();
            }

            $suscripcion = new Suscripciones();
            $suscripcion->usuario_id = $data['usuario_id'];
            $suscripcion->plan_id = $plan->id;
            $suscripcion->estado = 'activo';
            $suscripcion->fecha_inicio = now();
            $suscripcion->fecha_fin = $plan->duracion_dias
                ? now()->addDays((int) $plan->duracion_dias)
                : null;
            $suscripcion->creado_en = now();
            $suscripcion->save();

            $tipoCambio = 'CREACION';
            $planAnteriorObj = $planAnterior ? Planes::find($planAnterior) : null;

            if ($planAnteriorObj) {
                if ($plan->precio > $planAnteriorObj->precio) {
                    $tipoCambio = 'UPGRADE';
                } elseif ($plan->precio < $planAnteriorObj->precio) {
                    $tipoCambio = 'DOWNGRADE';
                } else {
                    $tipoCambio = 'CAMBIO_PLAN';
                }
            }

            Historialsuscripciones::create([
                'suscripcion_id' => $suscripcion->id,
                'previous_plan_id' => $planAnterior,
                'new_plan_id' => $suscripcion->plan_id,
                'changed_type' => $tipoCambio,
                'created_at' => now(),
            ]);

            return $suscripcion;
        });

        return response()->json([
            'message' => 'Suscripción creada',
            'suscripcion' => $suscripcion,
        ]);
    }






/*

    $suscripcion = Suscripciones::where('usuario_id', $usuario->id)
        ->where('estado', 'activo')
        ->whereDate('fecha_fin', '>=', now())
        ->first();

    
    if (!$suscripcion) {
        return response()->json([
            'error' => 'Tu suscripción ha expirado o no tienes plan'
        ], 403);
    }

    return response()->json([
        'message' => 'Login correcto',
        'usuario' => $usuario,
        'suscripcion' => $suscripcion
    ]);
*/

public function faker(Request $request)
    {
        $cantidad = $request->input('cantidad', 100);

        for ($i = 0; $i < $cantidad; $i++) {

            $inicio = Carbon::now()->subDays(rand(1, 180));

            DB::table('suscripciones')->insert([
                'usuario_id' => rand(1, 72),
                'plan_id' => rand(1, 3),
                'estado' => fake()->randomElement([
                    'activo',
                    'activo',
                    'activo',
                    'activo',
                    'inactivo'
                ]),
                'fecha_inicio' => $inicio->format('Y-m-d'),
                'fecha_fin' => $inicio->copy()->addDays(30)->format('Y-m-d'),
                'creado_en' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "$cantidad suscripciones creadas"
        ]);
    }






}