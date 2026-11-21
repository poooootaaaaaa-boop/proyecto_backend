<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecetasSeeder extends Seeder
{
    /**
     * Datos de la tabla `recetas` (60 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 2, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 3, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 4, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 5, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 6, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 7, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 8, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 9, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 10, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 11, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 12, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 13, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 14, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 15, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-25 09:43:16'],
            ['id' => 16, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-01-09 09:43:16'],
            ['id' => 17, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-04 09:43:16'],
            ['id' => 18, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-02-23 09:43:16'],
            ['id' => 19, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-01-19 09:43:16'],
            ['id' => 20, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-01-20 09:43:16'],
            ['id' => 21, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-01-23 09:43:16'],
            ['id' => 22, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-03 09:43:16'],
            ['id' => 23, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2025-12-29 09:43:16'],
            ['id' => 24, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-20 09:43:16'],
            ['id' => 25, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-02-23 09:43:16'],
            ['id' => 26, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-02-19 09:43:16'],
            ['id' => 27, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-02-04 09:43:16'],
            ['id' => 28, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2025-12-28 09:43:16'],
            ['id' => 29, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-01-13 09:43:16'],
            ['id' => 30, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-02-11 09:43:16'],
            ['id' => 31, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-01-05 09:43:16'],
            ['id' => 32, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-02-01 09:43:16'],
            ['id' => 33, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-02-21 09:43:16'],
            ['id' => 34, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-02-06 09:43:16'],
            ['id' => 35, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-03-04 09:43:16'],
            ['id' => 36, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-03-16 09:43:16'],
            ['id' => 37, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2025-12-26 09:43:16'],
            ['id' => 38, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2026-01-05 09:43:16'],
            ['id' => 39, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'pendiente', 'creado_en' => '2026-01-25 09:43:16'],
            ['id' => 40, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => 1, 'estado' => 'entregada', 'creado_en' => '2025-12-31 09:43:16'],
            ['id' => 41, 'consulta_id' => 17, 'doctor_id' => 1, 'paciente_id' => 12, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => null],
            ['id' => 42, 'consulta_id' => 19, 'doctor_id' => 1, 'paciente_id' => 103, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => null],
            ['id' => 43, 'consulta_id' => 21, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => null],
            ['id' => 44, 'consulta_id' => 22, 'doctor_id' => 1, 'paciente_id' => 104, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => null],
            ['id' => 45, 'consulta_id' => 23, 'doctor_id' => 1, 'paciente_id' => 104, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => null],
            ['id' => 46, 'consulta_id' => 24, 'doctor_id' => 1, 'paciente_id' => 2, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => null],
            ['id' => 47, 'consulta_id' => 25, 'doctor_id' => 1, 'paciente_id' => 104, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-04-01 10:13:15'],
            ['id' => 48, 'consulta_id' => 26, 'doctor_id' => 1, 'paciente_id' => 2, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-04-14 04:40:38'],
            ['id' => 49, 'consulta_id' => 27, 'doctor_id' => 1, 'paciente_id' => 103, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-04-14 05:01:14'],
            ['id' => 50, 'consulta_id' => 28, 'doctor_id' => 1, 'paciente_id' => 103, 'farmacia_id' => null, 'estado' => 'entregado', 'creado_en' => '2026-04-16 23:23:19'],
            ['id' => 51, 'consulta_id' => 29, 'doctor_id' => 1, 'paciente_id' => 2, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-04-16 23:56:42'],
            ['id' => 52, 'consulta_id' => 30, 'doctor_id' => 1, 'paciente_id' => 1, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-04-17 00:42:12'],
            ['id' => 53, 'consulta_id' => 31, 'doctor_id' => 26, 'paciente_id' => 105, 'farmacia_id' => null, 'estado' => 'entregado', 'creado_en' => '2026-04-17 02:54:02'],
            ['id' => 54, 'consulta_id' => 32, 'doctor_id' => 1, 'paciente_id' => 106, 'farmacia_id' => null, 'estado' => 'entregado', 'creado_en' => '2026-07-19 03:32:07'],
            ['id' => 55, 'consulta_id' => 33, 'doctor_id' => 28, 'paciente_id' => 107, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-07-28 04:24:03'],
            ['id' => 56, 'consulta_id' => 34, 'doctor_id' => 28, 'paciente_id' => 108, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-09-19 06:21:20'],
            ['id' => 57, 'consulta_id' => 36, 'doctor_id' => 28, 'paciente_id' => 109, 'farmacia_id' => null, 'estado' => 'entregado', 'creado_en' => '2026-10-10 07:38:31'],
            ['id' => 59, 'consulta_id' => null, 'doctor_id' => 15, 'paciente_id' => 6, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-10-16 22:42:57'],
            ['id' => 60, 'consulta_id' => null, 'doctor_id' => 11, 'paciente_id' => 6, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-10-16 23:52:44'],
            ['id' => 61, 'consulta_id' => null, 'doctor_id' => 1, 'paciente_id' => 10, 'farmacia_id' => null, 'estado' => 'pendiente', 'creado_en' => '2026-10-17 04:56:13'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('recetas')->insert($chunk);
        }
    }
}
