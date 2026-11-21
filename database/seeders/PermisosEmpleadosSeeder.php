<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosEmpleadosSeeder extends Seeder
{
    /**
     * Datos de la tabla `permisos_empleados` (1 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'empleado_id' => 1, 'tipo' => 'estudio', 'fecha_inicio' => '2026-10-30', 'fecha_fin' => '2026-11-05', 'dias' => 7, 'con_goce_sueldo' => 1, 'motivo' => 'mfomkmvop', 'estado' => 'pendiente', 'resuelto_por' => null, 'resuelto_en' => null, 'observaciones' => null, 'created_at' => '2026-10-31 04:23:36', 'updated_at' => '2026-10-31 04:23:36'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('permisos_empleados')->insert($chunk);
        }
    }
}
