<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            UsuarioNotificacionesSeeder::class,
        ]);
    }
}
