<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ControlAlimentosSeeder extends Seeder
{
    /**
     * Datos de la tabla `control_alimentos` (3 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'paciente_id' => 9, 'fecha_hora_recepcion' => '2026-09-24 23:38:00', 'estado_alimentos' => 'bueno', 'detalle_alimentos' => '[{"nombre": "manzana", "cantidad": 2}, {"nombre": "platano", "cantidad": 1}, {"nombre": "torta de jamon y queso", "cantidad": 1}]', 'alimentos_desechados' => null, 'motivo_desecho' => null, 'paciente_consumio' => 1, 'observaciones_nutricionales' => null, 'entrega_alimentos_paciente' => 1, 'created_at' => '2026-09-25 06:36:11', 'updated_at' => '2026-09-25 06:36:11'],
            ['id' => 2, 'paciente_id' => 5, 'fecha_hora_recepcion' => '2026-10-23 11:36:00', 'estado_alimentos' => 'bueno', 'detalle_alimentos' => '[{"nombre": "platano", "cantidad": 1}]', 'alimentos_desechados' => null, 'motivo_desecho' => 'comida', 'paciente_consumio' => 1, 'observaciones_nutricionales' => 'buena', 'entrega_alimentos_paciente' => 1, 'created_at' => '2026-10-07 23:36:45', 'updated_at' => '2026-10-07 23:36:45'],
            ['id' => 3, 'paciente_id' => 7, 'fecha_hora_recepcion' => '2026-10-24 15:41:00', 'estado_alimentos' => 'regular', 'detalle_alimentos' => '[{"nombre": "platano", "cantidad": 1}, {"nombre": "manzana", "cantidad": 4}]', 'alimentos_desechados' => null, 'motivo_desecho' => 'ninguno', 'paciente_consumio' => 1, 'observaciones_nutricionales' => 'buena alimentación', 'entrega_alimentos_paciente' => 0, 'created_at' => '2026-10-07 23:38:48', 'updated_at' => '2026-10-07 23:38:48'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('control_alimentos')->insert($chunk);
        }
    }
}
