<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsistenciasSeeder extends Seeder
{
    /**
     * Datos de la tabla `asistencias` (1 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'doctor_id' => 1, 'fecha' => '2026-09-26', 'hora_entrada' => '00:38:14', 'hora_salida' => null, 'horas_trabajadas' => null, 'created_at' => '2026-09-26 06:38:14', 'updated_at' => '2026-09-26 06:38:14'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('asistencias')->insert($chunk);
        }
    }
}
