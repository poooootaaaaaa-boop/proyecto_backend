<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrasladosHabitacionSeeder extends Seeder
{
    /**
     * Datos de la tabla `traslados_habitacion` (5 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'ocupacion_id' => 5, 'habitacion_origen_id' => 2, 'habitacion_destino_id' => 3, 'doctor_id' => null, 'motivo' => 'estara en mantenimiento esta habitacion', 'fecha_traslado' => '2026-09-25 05:46:44', 'created_at' => '2026-09-25 11:46:44', 'updated_at' => '2026-09-25 11:46:44'],
            ['id' => 2, 'ocupacion_id' => 8, 'habitacion_origen_id' => 3, 'habitacion_destino_id' => 4, 'doctor_id' => 6, 'motivo' => 'mantenimiento de la habitación', 'fecha_traslado' => '2026-10-16 13:06:00', 'created_at' => '2026-10-16 22:09:31', 'updated_at' => '2026-10-16 22:09:31'],
            ['id' => 3, 'ocupacion_id' => 6, 'habitacion_origen_id' => 1, 'habitacion_destino_id' => 2, 'doctor_id' => 6, 'motivo' => 'hihihih', 'fecha_traslado' => '2026-10-16 10:43:00', 'created_at' => '2026-10-16 22:44:21', 'updated_at' => '2026-10-16 22:44:21'],
            ['id' => 4, 'ocupacion_id' => 11, 'habitacion_origen_id' => 3, 'habitacion_destino_id' => 4, 'doctor_id' => 1, 'motivo' => 'por mantenimiento de la anterior habitación', 'fecha_traslado' => '2026-10-16 16:30:00', 'created_at' => '2026-10-17 04:53:52', 'updated_at' => '2026-10-17 04:53:52'],
            ['id' => 5, 'ocupacion_id' => 13, 'habitacion_origen_id' => 3, 'habitacion_destino_id' => 2, 'doctor_id' => 31, 'motivo' => 'traslado', 'fecha_traslado' => '2026-11-13 12:52:00', 'created_at' => '2026-11-14 00:53:28', 'updated_at' => '2026-11-14 00:53:28'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('traslados_habitacion')->insert($chunk);
        }
    }
}
