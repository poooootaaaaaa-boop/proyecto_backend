<?php

namespace App\Http\Controllers;

use App\Models\SesionDoctor;
use App\Models\Usuario;
use Illuminate\Http\Request;

class SesionDoctorController extends Controller
{
    /**
     * Registrar el inicio de sesión de un doctor.
     * Se llama desde AuthController después de reconocer el rostro.
     */
    public static function registrarInicio(
        Usuario $usuario,
        $accessToken
    ): void {
        // Solo registrar doctores: rol_id = 3
        if ((int) $usuario->rol_id !== 3) {
            return;
        }

        SesionDoctor::create([
            'usuario_id' => $usuario->id,
            'token_id' => $accessToken->accessToken->id,
            'inicio_sesion' => now(),
            'fin_sesion' => null,
            'estado' => 'activa',
        ]);
    }

    /**
     * Historial de entradas y salidas.
     */
    public function historial(Request $request)
    {
        $consulta = SesionDoctor::with([
            'usuario:id,nombre,correo,rol_id'
        ])->whereHas('usuario', function ($query) {
            $query->where('rol_id', 3);
        });

        if ($request->filled('desde')) {
            $consulta->whereDate(
                'inicio_sesion',
                '>=',
                $request->input('desde')
            );
        }

        if ($request->filled('hasta')) {
            $consulta->whereDate(
                'inicio_sesion',
                '<=',
                $request->input('hasta')
            );
        }

        if ($request->filled('usuario_id')) {
            $consulta->where(
                'usuario_id',
                $request->input('usuario_id')
            );
        }

        $sesiones = $consulta
            ->orderByDesc('inicio_sesion')
            ->paginate(50);

        $sesiones->getCollection()->transform(function ($sesion) {
            $sesion->doctor = $sesion->usuario?->nombre;
            $sesion->correo = $sesion->usuario?->correo;

            $sesion->duracion_segundos = $sesion->fin_sesion
                ? $sesion->inicio_sesion->diffInSeconds(
                    $sesion->fin_sesion
                )
                : null;

            return $sesion;
        });

        return response()->json($sesiones);
    }
}
