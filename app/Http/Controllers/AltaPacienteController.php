<?php

namespace App\Http\Controllers;

use App\Exceptions\LimitePlanExcedidoException;
use App\Models\AltaPaciente;
use App\Models\Usuario;
use App\Services\LimitePlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AltaPacienteController extends Controller
{
    public function __construct(private LimitePlanService $limites)
    {
    }

    public function addPaciente(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellidoP' => 'required',
            'correo' => 'required|email|unique:usuarios,correo',
            'doctor_id' => 'required|integer|exists:doctores,id',
            'face_embedding' => 'nullable|json',
        ]);

        $passwordPlano = Str::random(8);

        try {
            $usuario = DB::transaction(function () use ($request, $passwordPlano) {
                $this->limites->assertPuedeRegistrarPaciente((int) $request->doctor_id);

                $usuario = Usuario::create([
                    'nombre' => $request->nombre . ' ' . $request->apellidoP . ' ' . $request->apellidoM,
                    'correo' => $request->correo,
                    'telefono' => $request->telefono,
                    'password' => Hash::make($passwordPlano),
                    'rol_id' => 2,
                    'face_embedding' => $request->face_embedding,
                ]);

                $paciente = new AltaPaciente();
                $paciente->usuario_id = $usuario->id;
                $paciente->doctor_id = $request->doctor_id;
                $paciente->nacimiento = $request->nacimiento;
                $paciente->genero = $request->genero;
                $paciente->telefono = $request->telefono;
                $paciente->correo = $request->correo;
                $paciente->direccion = $request->direccion;
                $paciente->colonia = $request->colonia;
                $paciente->ciudad = $request->ciudad;
                $paciente->estado = $request->estado;
                $paciente->codigoPostal = $request->codigoPostal;
                $paciente->tipoSangre = $request->tipoSangre;
                $paciente->alergias = $request->alergias;
                $paciente->padecimientoHeredofamiliar = $request->padecimientoHeredofamiliar;
                $paciente->save();

                return $usuario;
            });
        } catch (LimitePlanExcedidoException $e) {
            return response()->json($e->toResponse(), 403);
        }

        return response()->json([
            'message' => 'Paciente creado',
            'usuario' => $usuario->correo,
            'password' => $passwordPlano,
        ]);
    }


public function getApiPaciente()
{
    $pacientes = AltaPaciente::with('usuario')->get();

    return response()->json([
        "paciente" => $pacientes->map(function ($p) {
            return [
                "id" => $p->id,
                "nombre" => $p->usuario->nombre ?? ''
            ];
        })
    ]);
}

}
