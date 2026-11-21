<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstanciaRegistrosSeeder extends Seeder
{
    /**
     * Datos de la tabla `estancia_registros` (7 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 1, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: el paciente se encuentra en buen estado ya no le duele el estomago\nTemperatura: 36.5 °C\nPresión arterial: 120/80 mmHg\nPulso: 73 lpm\nObservaciones: el paciente solo pose un estado en calma", 'costo' => null, 'fecha_hora' => '2026-10-16 21:26:06', 'created_at' => '2026-10-17 03:26:06', 'updated_at' => '2026-10-17 03:26:06'],
            ['id' => 2, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 1, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: bueno\nTemperatura: 36 °C\nPresión arterial: 120/80 mmHg\nPulso: 72 lpm\nObservaciones: sds", 'costo' => null, 'fecha_hora' => '2026-10-16 21:30:58', 'created_at' => '2026-10-17 03:30:58', 'updated_at' => '2026-10-17 03:30:58'],
            ['id' => 3, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 1, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: estado de paciente estable\nTemperatura: 35.3 °C\nPresión arterial: 120/80 mmHg\nPulso: 72 lpm\nObservaciones: buena observación", 'costo' => null, 'fecha_hora' => '2026-10-16 22:20:03', 'created_at' => '2026-10-17 04:20:03', 'updated_at' => '2026-10-17 04:20:03'],
            ['id' => 4, 'ocupacion_id' => 6, 'paciente_id' => 10, 'doctor_id' => 28, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: buena\nTemperatura: 37 °C\nPresión arterial: 120/80 mmHg\nPulso: 72 lpm\nObservaciones: buena", 'costo' => null, 'fecha_hora' => '2026-10-16 22:27:47', 'created_at' => '2026-10-17 04:27:47', 'updated_at' => '2026-10-17 04:27:47'],
            ['id' => 5, 'ocupacion_id' => 6, 'paciente_id' => 10, 'doctor_id' => 28, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: guug\nTemperatura: 36 °C\nPresión arterial: 120/80 mmHg\nPulso: 72 lpm\nObservaciones: jjojo", 'costo' => null, 'fecha_hora' => '2026-10-16 22:36:36', 'created_at' => '2026-10-17 04:36:36', 'updated_at' => '2026-10-17 04:36:36'],
            ['id' => 6, 'ocupacion_id' => 6, 'paciente_id' => 10, 'doctor_id' => 28, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: uuu\nTemperatura: 36 °C\nPresión arterial: 120/80 mmHg\nPulso: 72 lpm\nObservaciones: uuu", 'costo' => null, 'fecha_hora' => '2026-10-16 22:39:59', 'created_at' => '2026-10-17 04:39:59', 'updated_at' => '2026-10-17 04:39:59'],
            ['id' => 7, 'ocupacion_id' => 11, 'paciente_id' => 1, 'doctor_id' => 28, 'tipo' => 'observacion', 'descripcion' => "Registro de Farmacia\nDiagnóstico reportado: inniininni\nTemperatura: 37 °C\nPresión arterial: 120/80 mmHg\nPulso: 72 lpm\nObservaciones: jiijji", 'costo' => null, 'fecha_hora' => '2026-10-16 22:43:54', 'created_at' => '2026-10-17 04:43:54', 'updated_at' => '2026-10-17 04:43:54'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('estancia_registros')->insert($chunk);
        }
    }
}
