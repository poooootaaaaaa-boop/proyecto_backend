<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            EspecialidadesSeeder::class,
            UsuariosSeeder::class,
            ClinicasSeeder::class,
            FarmaciasSeeder::class,
            PlanesSeeder::class,
            TiposPagoDoctorSeeder::class,
            CategoriasMedicamentosSeeder::class,
            DistribuidoresSeeder::class,
            InstrumentosMedicosSeeder::class,
            MedicamentosSeeder::class,
            ConsultoriosSeeder::class,
            HabitacionesSeeder::class,
            DoctoresSeeder::class,
            DoctorConsultoriosSeeder::class,
            HorariosDoctoresSeeder::class,
            PacientesSeeder::class,
            InventarioSeeder::class,
            ConsultorioInstrumentosSeeder::class,
            CitasSeeder::class,
            ConsultasSeeder::class,
            RecetasSeeder::class,
            RecetaDetalleSeeder::class,
            BloqueosHorariosSeeder::class,
            OcupacionHabitacionesSeeder::class,
            SuscripcionesSeeder::class,
            HistorialPagosSeeder::class,
            HistorialSuscripcionesSeeder::class,
            PagosDoctoresSeeder::class,
            PagosDoctoresHistorialSeeder::class,
            NotificacionesSeeder::class,
            UsuarioNotificacionesSeeder::class,
            PreferenciasNotificacionesSeeder::class,
            ExpedienteArchivosSeeder::class,
            IALogsSeeder::class,
            MedicamentosCaducadosSeeder::class,
            MovimientosInventarioSeeder::class,
            UsersSeeder::class,
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