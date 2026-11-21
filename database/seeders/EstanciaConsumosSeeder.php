<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstanciaConsumosSeeder extends Seeder
{
    /**
     * Datos de la tabla `estancia_consumos` (4 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 1, 'insumo_id' => 2, 'cantidad' => '1.000', 'precio_unitario' => '230.00', 'subtotal' => '230.00', 'observaciones' => 'Registro de Farmacia', 'fecha_hora' => '2026-10-16 21:29:14', 'created_at' => '2026-10-17 03:29:14', 'updated_at' => '2026-10-17 03:29:14'],
            ['id' => 2, 'ocupacion_id' => 6, 'paciente_id' => 10, 'doctor_id' => 28, 'insumo_id' => 1, 'cantidad' => '3.000', 'precio_unitario' => '123.00', 'subtotal' => '369.00', 'observaciones' => 'Registro de Farmacia', 'fecha_hora' => '2026-10-16 22:39:59', 'created_at' => '2026-10-17 04:39:59', 'updated_at' => '2026-10-17 04:39:59'],
            ['id' => 3, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 28, 'insumo_id' => 1, 'cantidad' => '1.000', 'precio_unitario' => '123.00', 'subtotal' => '123.00', 'observaciones' => 'Registro de Farmacia: jji', 'fecha_hora' => '2026-10-16 22:43:54', 'created_at' => '2026-10-17 04:43:54', 'updated_at' => '2026-10-17 04:43:54'],
            ['id' => 4, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 28, 'insumo_id' => 2, 'cantidad' => '1.000', 'precio_unitario' => '230.00', 'subtotal' => '230.00', 'observaciones' => 'Registro de Farmacia: jiij', 'fecha_hora' => '2026-10-16 22:43:55', 'created_at' => '2026-10-17 04:43:55', 'updated_at' => '2026-10-17 04:43:55'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('estancia_consumos')->insert($chunk);
        }
    }
}
