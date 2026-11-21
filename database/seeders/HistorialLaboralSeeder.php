<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorialLaboralSeeder extends Seeder
{
    /**
     * Datos de la tabla `historial_laboral` (1 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'empleado_id' => 1, 'tipo' => 'alta', 'fecha' => '2026-10-30', 'descripcion' => 'Alta en la clínica como enfermero.', 'valor_anterior' => null, 'valor_nuevo' => 'enfermero', 'automatico' => 1, 'registrado_por' => 153, 'created_at' => '2026-10-30 12:50:44', 'updated_at' => '2026-10-30 12:50:44'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('historial_laboral')->insert($chunk);
        }
    }
}
