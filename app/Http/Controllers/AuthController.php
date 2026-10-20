<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Doctor;
use App\Models\Clinica;
use App\Models\Farmacia;
use App\Models\Especialidad;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Rol;

class AuthController extends Controller
{

    public function register(Request $request)
    {

$request->validate([
    'nombre' => 'required|string|max:120',
    'correo' => 'required|email|unique:usuarios,correo',
    'telefono' => 'nullable|string|max:20',
    'password' => 'required|min:6',
    'rol_id' => 'required|exists:roles,id',
    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // rostro
    'face_embedding' => 'nullable|json',

    // extras
    'nombre_clinica' => 'nullable|string|max:150',
    'direccion' => 'nullable|string',
    'ciudad' => 'nullable|string|max:80',
    'estado' => 'nullable|string|max:80',
    'pais' => 'nullable|string|max:80',

    'especialidad_id' => 'nullable',
    'especialidad_nombre' => 'nullable|string|max:150',
    'cedula_profesional' => 'nullable|string|max:30',
    'anios_exp' => 'nullable|integer|min:0',
]);

        DB::beginTransaction();

        try {

            //  Crear usuario
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password),
                'rol_id' => $request->rol_id,
                'activo' => true,
                'face_embedding' => $request->face_embedding,
            ]);

            //  ROLES

//  2 = DOCTOR
if ($request->rol_id == 3) {

    // MANEJO DE ESPECIALIDAD (AQUÍ VA)
    $especialidad_id = $request->especialidad_id;

    //  si no viene ID pero sí nombre → crear
    if (!$especialidad_id && $request->especialidad_nombre) {
        $especialidad = Especialidad::firstOrCreate([
            'nombre' => $request->especialidad_nombre
        ]);

        $especialidad_id = $especialidad->id;
    }

    Doctor::create([
        'usuario_id' => $usuario->id,
        'clinica_id' => $request->clinica_id ?? 1, // opcional
        'especialidad_id' => $especialidad_id, //  AQUÍ USAS LA VARIABLE
        'cedula_profesional' => $request->cedula_profesional,
        'telefono' => $request->telefono,
        'anios_exp' => $request->anios_exp 
    ]);
}

            //  3 = CLINICA / FARMACIA
            if ($request->rol_id == 4) {

                $clinica = Clinica::create([
                    'usuario_id' => $usuario->id,
                    'nombre' => $request->nombre_clinica,
                    'direccion' => $request->direccion,
                    'ciudad' => $request->ciudad,
                    'estado' => $request->estado,
                    'pais' => $request->pais ?? 'México',
                    'telefono' => $request->telefono
                ]);

                Farmacia::create([
                    'usuario_id' => $usuario->id,
                    'clinica_id' => $clinica->id,
                    'nombre' => $request->nombre_clinica,
                    'direccion' => $request->direccion,
                    'telefono' => $request->telefono
                ]);
            }

            DB::commit();

             if ($request->hasFile('foto')) {

    $archivo = $request->file('foto');

    $nombre_archivo = 'usuario-'.$usuario->id.'.'.$archivo->getClientOriginalExtension();

    $archivo->storeAs('fotos/usuarios', $nombre_archivo, 'public');

    $usuario->foto_url = $nombre_archivo;
    $usuario->save();
}


            $token = $usuario->createToken('token')->plainTextToken;

            return response()->json([
                'mensaje' => 'Usuario registrado correctamente',
                'usuario' => $usuario,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'mensaje' => 'Error al registrar',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::with(['paciente', 'doctor', 'clinica', 'farmacia', 'rol'])
    ->where('correo', $request->correo)
    ->first();

        // Usuario no existe
        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado'
            ], 404);
        }

        // Password incorrecto
        if (!Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'mensaje' => 'Contraseña incorrecta'
            ], 401);
        }

        // Usuario inactivo
        if (!$usuario->activo) {
            return response()->json([
                'mensaje' => 'Usuario desactivado'
            ], 403);
        }

        // Eliminar tokens anteriores (opcional pero PRO)
        $usuario->tokens()->delete();

        // Crear nuevo token
        $token = $usuario->createToken('token')->plainTextToken;

  return response()->json([
    'mensaje' => 'Login exitoso',
    'usuario' => [
        'id' => $usuario->id,
        'nombre' => $usuario->nombre,
        'correo' => $usuario->correo,
        'foto_url' => $usuario->foto_url, // CLAVE
        'rol' => $usuario->rol->nombre,
        'paciente_id' => $usuario->paciente?->id,
'doctor_id' => $usuario->doctor?->id,
'clinica_id' => $usuario->clinica?->id,
'farmacia_id' => $usuario->farmacia?->id,
    ],
    'token' => $token
]);
    }









public function loginRostro(Request $request)
{
    $request->validate([
        'face_embedding' => 'required|json',
    ]);

    $embeddingRecibido = json_decode(
        $request->face_embedding,
        true
    );

    if (!is_array($embeddingRecibido) || count($embeddingRecibido) !== 1024) {
        return response()->json([
            'mensaje' => 'Embedding facial inválido'
        ], 422);
    }

    $usuarios = Usuario::with([
        'paciente',
        'doctor',
        'clinica',
        'farmacia',
        'rol'
    ])
    ->where('activo', true)
    ->whereNotNull('face_embedding')
    ->get();

    $mejorUsuario = null;
    $mejorSimilitud = 0;

    foreach ($usuarios as $usuario) {

        $embeddingGuardado = json_decode(
            $usuario->face_embedding,
            true
        );

        if (!is_array($embeddingGuardado)) {
            continue;
        }

        if (count($embeddingGuardado) !== 1024) {
            continue;
        }

        $similitud = $this->calcularSimilitud(
            $embeddingRecibido,
            $embeddingGuardado
        );

        if ($similitud > $mejorSimilitud) {
            $mejorSimilitud = $similitud;
            $mejorUsuario = $usuario;
        }
    }

    // Umbral inicial
    $umbral = 0.70;

    if (!$mejorUsuario || $mejorSimilitud < $umbral) {
        return response()->json([
            'mensaje' => 'Rostro no reconocido',
            'similitud' => $mejorSimilitud
        ], 401);
    }

    // Eliminar tokens anteriores
    $mejorUsuario->tokens()->delete();

    // Crear nuevo token
    $token = $mejorUsuario
        ->createToken('token')
        ->plainTextToken;

    return response()->json([
        'mensaje' => 'Login facial exitoso',
        'similitud' => $mejorSimilitud,
        'usuario' => [
            'id' => $mejorUsuario->id,
            'nombre' => $mejorUsuario->nombre,
            'correo' => $mejorUsuario->correo,
            'foto_url' => $mejorUsuario->foto_url,
            'rol' => $mejorUsuario->rol->nombre,
            'paciente_id' => $mejorUsuario->paciente?->id,
            'doctor_id' => $mejorUsuario->doctor?->id,
            'clinica_id' => $mejorUsuario->clinica?->id,
            'farmacia_id' => $mejorUsuario->farmacia?->id,
        ],
        'token' => $token
    ]);
}




private function calcularSimilitud(array $a, array $b): float
{
    $producto = 0;
    $normaA = 0;
    $normaB = 0;

    for ($i = 0; $i < count($a); $i++) {

        $producto += $a[$i] * $b[$i];

        $normaA += $a[$i] * $a[$i];

        $normaB += $b[$i] * $b[$i];
    }

    if ($normaA == 0 || $normaB == 0) {
        return 0;
    }

    return $producto / (
        sqrt($normaA) * sqrt($normaB)
    );
}










































    // AUTOLOGIN (Sanctum)
    public function autologin(Request $request)
    {
        return response()->json([
            'usuario' => $request->user()
        ]);
    }


    // LOGOUT (solo sesión actual)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente'
        ]);
    }


    // LOGOUT ALL (opcional PRO)
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'mensaje' => 'Todas las sesiones cerradas'
        ]);
    }

    public function forgotPassword(Request $request)
{
    $request->validate([
        'correo' => 'required|email'
    ]);

    $usuario = Usuario::where('correo', $request->correo)->first();

    if (!$usuario) {
        return response()->json(['mensaje' => 'Correo no encontrado'], 404);
    }

    $token = Str::random(60);

    DB::table('password_resets')->updateOrInsert(
        ['correo' => $request->correo],
        ['token' => $token, 'created_at' => now()]
    );

    return response()->json([
        'mensaje' => 'Token generado',
        'token' => $token //  solo para pruebas
    ]);
}
}