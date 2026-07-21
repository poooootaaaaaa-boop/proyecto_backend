<?php

namespace App\Http\Controllers;

use App\Models\DoctorModel;
use App\Models\Especialidad;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre'              => 'required|string|max:120',
            'correo'               => 'required|email|unique:usuarios,correo',
            'password'             => 'required|min:6',
            'especialidad_id'      => 'nullable|exists:especialidades,id',
            'cedula_profesional'   => 'required|string|max:30|unique:doctores,cedula_profesional',
            'anios_exp'            => 'nullable|integer|min:0',
            'telefono'             => 'nullable|string|max:20',
            'imagen'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // clinica_id ya no se hardcodea a 1: se toma del usuario autenticado
            // o se recibe explícito si el flujo lo requiere (ej. superadmin creando en otra clínica).
            'clinica_id'           => 'nullable|integer|exists:clinicas,id',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear usuario
            $usuario = Usuario::create([
                'rol_id'    => 3,
                'nombre'    => $request->nombre,
                'correo'    => $request->correo,
                'telefono'  => $request->telefono,
                'password'  => Hash::make($request->password),
                'foto_url'  => null,
            ]);

            // 2. Subir foto (si existe)
            if ($request->hasFile('imagen')) {
                $archivo = $request->file('imagen');
                $nombreArchivo = 'usuario-' . $usuario->id . '.' . $archivo->getClientOriginalExtension();

                $archivo->storeAs('fotos/usuarios', $nombreArchivo, 'public');

                $usuario->foto_url = $nombreArchivo;
                $usuario->save();
            }

            // 3. Crear doctor
            $doctor = DoctorModel::create([
                'usuario_id'          => $usuario->id,
                'clinica_id'          => $request->clinica_id ?? $request->user()?->clinica_id ?? 1,
                'especialidad_id'     => $request->especialidad_id,
                'cedula_profesional'  => $request->cedula_profesional,
                'anios_exp'           => $request->anios_exp,
                'telefono'            => $request->telefono,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Doctor creado correctamente',
                'usuario' => $usuario,
                'doctor'  => $doctor,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Index unificado: doctores + relaciones + presencia en tiempo real.
     * Antes hacía una consulta a `asistencias` POR CADA doctor (N+1).
     * Ahora se resuelve con una sola subconsulta vía withExists.
     */
    public function index()
    {
        $doctores = DoctorModel::with(['usuario', 'especialidad'])
            ->withExists(['asistencias as tiene_asistencia_abierta' => function ($query) {
                $query->whereNull('hora_salida');
            }])
            ->get();

        $doctores->each(function ($doctor) {
            $doctor->estado_asistencia = $doctor->tiene_asistencia_abierta ? 'dentro' : 'fuera';
            unset($doctor->tiene_asistencia_abierta);
        });

        return response()->json($doctores);
    }

    /**
     * Usado por /doctores-completo (ConfigurarPagoDoctor, AsignarDoctorConsultorio).
     * Devuelve doctores con su usuario y especialidad ya cargados.
     */
    public function listarConUsuario()
    {
        $doctores = DoctorModel::with(['usuario', 'especialidad'])->get();

        return response()->json($doctores);
    }

    /**
     * Actualizar datos del doctor.
     * Antes usaba $request->all() sin validar, lo que permitía sobrescribir
     * cualquier columna (usuario_id, clinica_id, id, etc.) vía mass assignment.
     */
    public function update(Request $request, $id)
    {
        $doctor = DoctorModel::findOrFail($id);

        $validated = $request->validate([
            'especialidad_id'     => 'nullable|exists:especialidades,id',
            'cedula_profesional'  => 'sometimes|string|max:30|unique:doctores,cedula_profesional,' . $doctor->id,
            'anios_exp'           => 'nullable|integer|min:0',
            'telefono'            => 'nullable|string|max:20',
        ]);

        $doctor->update($validated);

        return response()->json([
            'message' => 'Doctor actualizado',
            'data'    => $doctor,
        ]);
    }

    /**
     * Eliminar doctor.
     */
    public function destroy($id)
    {
        $doctor = DoctorModel::findOrFail($id);
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor eliminado',
        ]);
    }

    /**
     * Catálogo de especialidades para los selects del frontend.
     */
    public function getEspecialidades()
    {
        return response()->json(Especialidad::all());
    }
}
