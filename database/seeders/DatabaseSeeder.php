<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Orden calculado segun las llaves foraneas de clinicam.sql:
     * cada tabla se siembra despues de las tablas de las que depende.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
<<<<<<< HEAD
=======
            EspecialidadesSeeder::class,
>>>>>>> f3d217348004769c5712e2314f229b6099d76cc4
            UsuariosSeeder::class,
            ClinicasSeeder::class,
            EspecialidadesSeeder::class,
            DoctoresSeeder::class,
            AsistenciasSeeder::class,
            BloqueosHorariosSeeder::class,
            CategoriasMedicamentosSeeder::class,
            PacientesSeeder::class,
            CitasSeeder::class,
            ConsentimientosSeeder::class,
            ConsentimientoFirmasSeeder::class,
            ConsentimientoHistorialSeeder::class,
            HabitacionesSeeder::class,
            ConsultasSeeder::class,
            FarmaciasSeeder::class,
            RecetasSeeder::class,
            OcupacionHabitacionesSeeder::class,
            ConsentimientoHospitalizacionSeeder::class,
            ConsentimientoPdfsSeeder::class,
            ConsentimientoVigilanciaSeeder::class,
            ConsultoriosSeeder::class,
            InstrumentosMedicosSeeder::class,
            ConsultorioInstrumentosSeeder::class,
            ControlAlimentosSeeder::class,
            DistribuidoresSeeder::class,
            DoctorConsultoriosSeeder::class,
            PuestosSeeder::class,
            EmpleadosSeeder::class,
            InsumosEstanciaSeeder::class,
            EstanciaConsumosSeeder::class,
            EstanciaRegistrosSeeder::class,
            ExpedienteArchivosSeeder::class,
            FirmasRegistradasSeeder::class,
            FormatosConsentimientoSeeder::class,
            HistorialLaboralSeeder::class,
            HistorialPagosSeeder::class,
            PlanesSeeder::class,
            SuscripcionesSeeder::class,
            HistorialSuscripcionesSeeder::class,
            HorariosDoctoresSeeder::class,
            HorariosEmpleadosSeeder::class,
            HospitalizacionDocumentosSeeder::class,
            IALogsSeeder::class,
            MedicamentosSeeder::class,
            InventarioSeeder::class,
            MedicamentosCaducadosSeeder::class,
            MovimientosInventarioSeeder::class,
            NotificacionesSeeder::class,
            TiposPagoDoctorSeeder::class,
            PagosDoctoresSeeder::class,
            PagosDoctoresHistorialSeeder::class,
            PermisosEmpleadosSeeder::class,
            PreferenciasNotificacionesSeeder::class,
            RecetaDetalleSeeder::class,
            ReporteHabitacionsSeeder::class,
            SesionesDoctoresSeeder::class,
            TrasladosHabitacionSeeder::class,
            TrasladoFirmasSeeder::class,
            UsersSeeder::class,
<<<<<<< HEAD
            UsuarioNotificacionesSeeder::class,
=======
>>>>>>> f3d217348004769c5712e2314f229b6099d76cc4
        ]);

        // Resetea todas las secuencias de PostgreSQL después de sembrar,
        // ya que varios seeders insertan con IDs fijos manualmente.
        $this->resetearSecuencias();
    }

    /**
     * Resetea automáticamente todas las secuencias de PostgreSQL
     * para que coincidan con el MAX(id) real de cada tabla.
     * Evita errores de "duplicate key" al insertar nuevos registros
     * después de correr los seeders.
     */
private function resetearSecuencias(): void
{
    $tablas = DB::select("
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = 'public'
        AND table_type = 'BASE TABLE'
    ");

    foreach ($tablas as $tabla) {
        $nombreTabla = $tabla->table_name;

        // Verifica si la tabla tiene una columna 'id' antes de intentar resetear su secuencia
        $tieneColumnaId = DB::selectOne("
            SELECT column_name
            FROM information_schema.columns
            WHERE table_schema = 'public'
            AND table_name = ?
            AND column_name = 'id'
        ", [$nombreTabla]);

        if (!$tieneColumnaId) {
            continue; // Salta tablas sin columna 'id' (como cache, jobs, sessions)
        }

        $secuencia = DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') as seq", [$nombreTabla]);

        if ($secuencia && $secuencia->seq) {
            DB::statement("
                SELECT setval(
                    pg_get_serial_sequence('{$nombreTabla}', 'id'),
                    COALESCE((SELECT MAX(id) FROM \"{$nombreTabla}\"), 1)
                )
            ");
        }
    }
}
}