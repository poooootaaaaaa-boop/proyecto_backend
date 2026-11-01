<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function crearContextoEstanciaTest(): array
{
    $rolId = DB::table('roles')->insertGetId(['nombre' => 'Doctor']);
    $usuarioClinicaId = DB::table('usuarios')->insertGetId([
        'rol_id' => $rolId,
        'nombre' => 'Clínica de prueba',
        'correo' => 'clinica-estancia@example.test',
    ]);
    $clinicaId = DB::table('clinicas')->insertGetId([
        'usuario_id' => $usuarioClinicaId,
        'nombre' => 'Clínica de prueba',
    ]);
    $usuarioDoctorId = DB::table('usuarios')->insertGetId([
        'rol_id' => $rolId,
        'nombre' => 'Doctora asignada',
        'correo' => 'doctora-estancia@example.test',
    ]);
    $doctorId = DB::table('doctores')->insertGetId([
        'usuario_id' => $usuarioDoctorId,
        'clinica_id' => $clinicaId,
    ]);
    $usuarioPacienteId = DB::table('usuarios')->insertGetId([
        'rol_id' => $rolId,
        'nombre' => 'Paciente de prueba',
        'correo' => 'paciente-estancia@example.test',
    ]);
    $pacienteId = DB::table('pacientes')->insertGetId([
        'usuario_id' => $usuarioPacienteId,
        'doctor_id' => $doctorId,
    ]);
    $habitacionId = DB::table('habitaciones')->insertGetId([
        'clinica_id' => $clinicaId,
        'numero' => '101',
        'tipo' => 'Individual',
        'estado' => 'Ocupada',
    ]);
    $ocupacionId = DB::table('ocupacion_habitaciones')->insertGetId([
        'habitacion_id' => $habitacionId,
        'paciente_id' => $pacienteId,
        'fecha_ingreso' => now()->subDay(),
        'estado' => 'Activa',
    ]);
    return compact('doctorId', 'pacienteId', 'ocupacionId');
}

it('creates, edits, and deactivates a stay supply in the database catalog', function () {
    $this->postJson('/api/insumos-estancia', [
        'nombre' => 'Solución salina',
        'categoria' => 'suero',
        'unidad_medida' => 'bolsa',
        'precio_unitario' => 38.50,
    ])->assertCreated()->assertJsonPath('data.activo', true);

    $insumoId = DB::table('insumos_estancia')->value('id');

    $this->patchJson("/api/insumos-estancia/{$insumoId}", ['precio_unitario' => 42.25])
        ->assertOk()->assertJsonPath('data.precio_unitario', '42.25');

    $this->deleteJson("/api/insumos-estancia/{$insumoId}")
        ->assertOk()->assertJsonPath('data.activo', false);

    $this->getJson('/api/insumos-estancia')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/insumos-estancia?incluir_inactivos=1')->assertOk()->assertJsonCount(1, 'data');
});

it('records assigned-doctor activities and prices consumption from the catalog', function () {
    ['doctorId' => $doctorId, 'ocupacionId' => $ocupacionId] = crearContextoEstanciaTest();
    $insumoId = DB::table('insumos_estancia')->insertGetId([
        'nombre' => 'Apósito estéril',
        'categoria' => 'material_medico',
        'unidad_medida' => 'pieza',
        'precio_unitario' => 12.50,
        'activo' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->postJson("/api/hospitalizaciones/{$ocupacionId}/evolucion", [
        'doctor_id' => $doctorId,
        'descripcion' => 'Paciente estable, tolera vía oral.',
    ])->assertCreated();

    $this->postJson("/api/hospitalizaciones/{$ocupacionId}/procedimientos", [
        'doctor_id' => $doctorId,
        'descripcion' => 'Curación de herida',
        'costo' => 75.00,
    ])->assertCreated();

    $this->postJson("/api/hospitalizaciones/{$ocupacionId}/consumos", [
        'doctor_id' => $doctorId,
        'insumo_id' => $insumoId,
        'cantidad' => 2,
    ])->assertCreated()->assertJsonPath('data.subtotal', '25.00');

    $this->patchJson("/api/insumos-estancia/{$insumoId}", ['precio_unitario' => 99.00])->assertOk();

    $this->getJson("/api/hospitalizaciones/{$ocupacionId}/seguimiento?doctor_id={$doctorId}")
        ->assertOk()
        ->assertJsonPath('data.costo_procedimientos', 75)
        ->assertJsonPath('data.costo_total', 100)
        ->assertJsonCount(2, 'data.registros')
        ->assertJsonCount(1, 'data.consumos');

    $usuarioNoAsignado = DB::table('usuarios')->insertGetId([
        'rol_id' => DB::table('roles')->value('id'),
        'nombre' => 'Doctor no asignado',
        'correo' => 'otro-doctor-estancia@example.test',
    ]);
    $otroDoctor = DB::table('doctores')->insertGetId([
        'usuario_id' => $usuarioNoAsignado,
        'clinica_id' => DB::table('clinicas')->value('id'),
    ]);

    $this->postJson("/api/hospitalizaciones/{$ocupacionId}/observaciones", [
        'doctor_id' => $otroDoctor,
        'descripcion' => 'Intento no autorizado',
    ])->assertForbidden();

    DB::table('ocupacion_habitaciones')->where('id', $ocupacionId)->update([
        'estado' => 'Alta',
        'fecha_salida' => now(),
    ]);

    $this->get("/api/hospitalizaciones/{$ocupacionId}/reporte-final/pdf")
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect(DB::table('hospitalizacion_documentos')
        ->where('ocupacion_id', $ocupacionId)
        ->where('tipo', 'Reporte estancia')
        ->exists())->toBeTrue();
});
