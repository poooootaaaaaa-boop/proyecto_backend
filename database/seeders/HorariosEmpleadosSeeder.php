<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorariosEmpleadosSeeder extends Seeder
{
    /**
     * Datos de la tabla `horarios_empleados` (5 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'empleado_id' => 1, 'dia_semana' => 1, 'hora_inicio' => '08:00:00', 'hora_fin' => '16:00:00', 'activo' => 1, 'created_at' => '2026-10-30 12:52:43', 'updated_at' => '2026-10-30 12:52:43'],
            ['id' => 2, 'empleado_id' => 1, 'dia_semana' => 2, 'hora_inicio' => '08:00:00', 'hora_fin' => '16:00:00', 'activo' => 1, 'created_at' => '2026-10-30 12:52:43', 'updated_at' => '2026-10-30 12:52:43'],
            ['id' => 3, 'empleado_id' => 1, 'dia_semana' => 3, 'hora_inicio' => '08:00:00', 'hora_fin' => '16:00:00', 'activo' => 1, 'created_at' => '2026-10-30 12:52:43', 'updated_at' => '2026-10-30 12:52:43'],
            ['id' => 4, 'empleado_id' => 1, 'dia_semana' => 4, 'hora_inicio' => '08:00:00', 'hora_fin' => '16:00:00', 'activo' => 1, 'created_at' => '2026-10-30 12:52:43', 'updated_at' => '2026-10-30 12:52:43'],
            ['id' => 5, 'empleado_id' => 1, 'dia_semana' => 5, 'hora_inicio' => '08:00:00', 'hora_fin' => '16:00:00', 'activo' => 1, 'created_at' => '2026-10-30 12:52:43', 'updated_at' => '2026-10-30 12:52:43'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('horarios_empleados')->insert($chunk);
        }
    }
}
