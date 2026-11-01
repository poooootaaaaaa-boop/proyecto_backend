<?php

namespace App\Http\Controllers;
use App\Models\Paciente;
use App\Models\AltaPaciente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 

class AltaPacienteController extends Controller
{
public function addPaciente(Request $request)
{
    $request->validate([
        'nombre' => 'required',
        'apellidoP' => 'required',
        'correo' => 'required|email|unique:usuarios,correo',
        'face_embedding' => 'nullable|json',
    ]);

    // contraseña automática
    $passwordPlano = Str::random(8);

    //  CREAR USUARIO
    $usuario = Usuario::create([
        'nombre' => $request->nombre . ' ' . $request->apellidoP . ' ' . $request->apellidoM,
        'correo' => $request->correo,
        'telefono' => $request->telefono,
        'password' => Hash::make($passwordPlano),
        'rol_id' => 2,//  2 = PACIENTE
            // Guardar el embedding facial
        'face_embedding' => $request->face_embedding,
    ]);

    //  CREAR PACIENTE
    $paciente = new AltaPaciente();

    $paciente->usuario_id = $usuario->id;
    $paciente->doctor_id = $request->doctor_id; //  AQUI LO ASIGNAS FIJO

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

    return response()->json([
        "message" => "Paciente creado",
        "usuario" => $usuario->correo,
        "password" => $passwordPlano
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
