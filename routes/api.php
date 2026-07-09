<?php

use App\Models\User;
use App\Models\Usuario;
use App\Models\HistorialPago;
use Illuminate\Support\Facades\Hash;


use App\Http\Controllers\DoctorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\DistribuidorController;
use App\Http\Controllers\AltaPacienteController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardFarmaciaController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PlanesController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ConsultorioController;
use App\Http\Controllers\TipoPagoDoctorController;
use App\Http\Controllers\PagoDoctorController;
use App\Http\Controllers\HorarioDoctorController;
use App\Http\Controllers\MedicamentosCaducadosController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\InstrumentoMedicoController;
use App\Http\Controllers\ConsultorioInstrumentoController;
use App\Http\Controllers\OrdenCompraController;

/*NUEVO*/


Route::apiResource(
    'habitaciones',
    HabitacionController::class
);

Route::apiResource(
    'instrumentos',
    InstrumentoMedicoController::class
);
Route::get(
    '/consultorio-instrumentos/inventario',
    [ConsultorioInstrumentoController::class, 'inventario']
);
Route::apiResource(
    'consultorio-instrumentos',
    ConsultorioInstrumentoController::class
);


Route::get('/consultorio-instrumentos',[ConsultorioInstrumentoController::class, 'index']);




Route::get(
    '/pagos-doctores',
    [PagoDoctorController::class,'index']
);

Route::post(
    '/pagos-doctores',
    [PagoDoctorController::class,'store']
);

Route::post(
    '/asignar-doctor-consultorio',
    [HorarioDoctorController::class,'store']
);

Route::delete(
    '/pagos-doctores/{id}',
    [PagoDoctorController::class,'destroy']
);

Route::get('/doctores-completo', [DoctorController::class,'listarConUsuario']);

Route::get('/tipos-pago', [TipoPagoDoctorController::class, 'index']);

Route::post('/tipos-pago', [TipoPagoDoctorController::class, 'store']);

Route::delete('/tipos-pago/{id}', [TipoPagoDoctorController::class, 'destroy']);

Route::get('/consultorios', [ConsultorioController::class,'index']);

Route::post('/consultorios', [ConsultorioController::class,'store']);

Route::post('/pagos-doctores', [PagoDoctorController::class, 'store']);
/*Nuevo*/ 


Route::put('/recetas/{id}', [RecetaController::class, 'actualizarEstado']);
Route::get('/farmacia/recetas-hoy', [DashboardFarmaciaController::class, 'recetasHoy']);

Route::get('/recetas/paciente/{id}', [ConsultaController::class, 'recetasPorPaciente']);
Route::get('/consulta/paciente/{id}/ultima', [ConsultaController::class, 'ultimaConsultaConReceta']);

Route::get('/citas/paciente/{id}/futuras', [CitaController::class, 'citasFuturas']);
Route::get('/citas/paciente/{id}/pasadas', [CitaController::class, 'citasPasadas']);

Route::get('/paciente/doctores/{usuarioId}', [CitaController::class, 'getDoctores']);
Route::get('/paciente/doctor/{usuario_id}', [CitaController::class, 'obtenerDoctorPaciente']);
Route::get('/citas/paciente/{paciente_id}', [CitaController::class, 'citasDelDoctorPorPaciente']);
Route::post('/citas/paciente', [CitaController::class, 'storeDesdePaciente']);
Route::get('/tratamientos-largos/{doctor_id}', [ConsultaController::class, 'tratamientosLargos']);
Route::get('/usuario/foto/{nombre}', [UsuarioController::class, 'obtenerFotoUsuario']);

Route::put('/citas/{id}/estado', [CitaController::class, 'cambiarEstado']);
Route::get('/pacientes-doctor/{doctor_id}', [CitaController::class, 'pacientesPorDoctor']);
Route::post('/citas', [CitaController::class, 'store']);
Route::get('/citas-doctor/{doctor_id}', [CitaController::class, 'citasPorDoctor']);

///////////RUTAS MOVIMIENTO INVENTARIO//////////
Route::get('/medicamentosselct', [MedicamentoController::class, 'index']);
Route::get('/proveedores', [DistribuidorController::class, 'proveedores']);
Route::get('/recetas', [RecetaController::class, 'recetas']);
Route::get('/recetas/farmacia', [RecetaController::class, 'index']);
Route::get('/recetas/stats', [RecetaController::class, 'stats']);
Route::get('/movimientos', [MovimientoInventarioController::class, 'index']);
Route::post('/guardarMovimientos', [MovimientoInventarioController::class, 'store']);
///////////RUTAS MOVIMIENTO INVENTARIO//////////


//Dashboard Farmacia
Route::get('/dashboard-farmacia', [DashboardFarmaciaController::class, 'index']);
Route::get('/dashboard-farmacia/imagen/{nombre_imagen}', [DashboardFarmaciaController::class, 'mostrar_imagen_usuario']);

////////especialidades//////////
Route::get('/especialidades', [EspecialidadController::class, 'index']);
Route::post('/especialidades', [EspecialidadController::class, 'store']);
////////especialidades//////////

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/////medicamentos////////////////
Route::get('/medicamentos', [MedicamentoController::class, 'listado'])->name('index_medicamentos');
Route::get('/medicamentos/formulario/{id?}', [MedicamentoController::class, 'formulario']);
Route::post('/medicamentos/operaciones', [MedicamentoController::class, 'operaciones']);
Route::get('/medicamentos/imagen/{nombre}', [MedicamentoController::class, 'mostrar_imagen']); 
Route::get('/categorias', [MedicamentoController::class, 'categorias']);

/////medicamentos////////////////






//-------------------------------------------prueba farmacias-------------------------------------------------


Route::post('/distribuidores', [DistribuidorController::class, 'addDistribuidor']);
Route::get('/MostrarDistribuidor', [DistribuidorController::class, 'getApiDistribuidor']);
Route::delete('/DeleteDistribuidor/{id}', [DistribuidorController::class, 'deleteDistribuidor']);
Route::get('/distribuidores/select', [DistribuidorController::class, 'getDistribuidoresSelect']);
Route::put("UpdateDistribuidor/{id}",[DistribuidorController::class, "putApiUpdateDistribuidor"]);


//-------------------------------------------prueba Doctores-------------------------------------------------
Route::post('/AltaPaciente', [AltaPacienteController::class, 'addPaciente']);
Route::get('/MostrarPaciente', [AltaPacienteController::class, 'getApiPaciente']);


//-------------------------------------------Consulta--------------------------------------------------------
Route::post('/AddConsulta', [ConsultaController::class, 'addConsulta']);
Route::get('/MostrarConsulta', [ConsultaController::class, 'getApiConsulta']);
Route::post('/finalizar-consulta', [ConsultaController::class, 'finalizarConsulta']);


///erick
Route::get('/consultas/{pacienteId}', [ConsultaController::class, 'getByPaciente']);
use App\Http\Controllers\PerfilController;

Route::post('/perfil/actualizar', [PerfilController::class, 'actualizarPerfil']);

// Obtener notificaciones del usuario
Route::get('/notificaciones/{usuario_id}', [NotificacionesController::class, 'obtenerNotificaciones']);

// Activar / desactivar notificación
Route::post('/notificaciones/toggle', [NotificacionesController::class, 'toggleNotificacion']);

Route::post('/perfil/cambiar-password', [PerfilController::class, 'cambiarPassword']);


Route::get('/perfil/doctor/{usuario_id}', [PerfilController::class, 'obtenerDoctor']);

Route::get('/perfil/{id}', [PerfilController::class, 'obtenerPerfil']);









Route::get('/doctores',[DoctorController::class,'index']);
Route::post('/doctores',[DoctorController::class,'store']);
Route::put('/doctores/{id}',[DoctorController::class,'update']);
Route::delete('/doctores/{id}',[DoctorController::class,'destroy']);



//->middleware('check.suscripcion');

Route::post('/AddPlanes', [PlanesController::class, 'AddPlanes']);
Route::get('/Getplanes', [PlanesController::class, 'GetPlanes']);

Route::post('/add-suscripcion', [SuscripcionController::class, 'AddSuscripcion']);

/*
Route::post('/pacientes', [PacienteController::class, 'store'])
    ->middleware('check.suscripcion');
*/



Route::post('/stripe-session', [StripeController::class, 'crearSesion']);
Route::post('/stripe-webhook', [StripeController::class, 'webhook']);


Route::post('/pago', [StripeController::class, 'pagar']);




Route::post('/medicamentoCaducado', [MedicamentosCaducadosController::class, 'store']);
Route::get('/getcaducados', [MedicamentosCaducadosController::class, 'getCaducados']);


///erick

Route::get('/doctores', [DoctorController::class, 'index']);

Route::get('/especialidades', [DoctorController::class, 'getEspecialidades']);

// Historial global para el módulo de clínicas
Route::get('/clinic/asistencias', [AsistenciaController::class, 'clinicIndex']);

// Acciones de autogestión de asistencia del doctor
Route::post('/asistencias', [AsistenciaController::class, 'stores']);
Route::get('/doctores/{id}/asistencias', [AsistenciaController::class, 'doctorIndex']);




Route::post('/ordenes-compra', [OrdenCompraController::class, 'addOrdenCompra']);
Route::get('/ordenes-compra', [OrdenCompraController::class, 'getOrdenesCompra']);
Route::get('/ordenes-compra/{id}', [OrdenCompraController::class, 'getOrdenCompra']);
Route::delete('/ordenes-compra/{id}', [OrdenCompraController::class, 'deleteOrdenCompra']);