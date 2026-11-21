<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsentimientoHospitalizacionSeeder extends Seeder
{
    /**
     * Datos de la tabla `consentimiento_hospitalizacion` (15 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'consentimiento_id' => 6, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-20', 'dias_estimados' => 5, 'habitacion_id' => 1, 'ocupacion_id' => 1, 'diagnostico' => 'en vigilacian', 'motivo' => 'en estado de recu[eracion de estres', 'created_at' => '2026-09-21 00:06:28', 'updated_at' => '2026-09-21 00:06:28'],
            ['id' => 2, 'consentimiento_id' => 7, 'tipo' => 'obstetricia', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-20', 'dias_estimados' => 4, 'habitacion_id' => null, 'ocupacion_id' => null, 'diagnostico' => 'ninguno', 'motivo' => 'reposo deespues del parto', 'created_at' => '2026-09-21 00:30:52', 'updated_at' => '2026-09-21 00:30:52'],
            ['id' => 3, 'consentimiento_id' => 8, 'tipo' => 'urgencias', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-20', 'dias_estimados' => 3, 'habitacion_id' => null, 'ocupacion_id' => null, 'diagnostico' => '2', 'motivo' => '2', 'created_at' => '2026-09-21 04:28:43', 'updated_at' => '2026-09-21 04:28:43'],
            ['id' => 4, 'consentimiento_id' => 9, 'tipo' => 'urgencias', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-20', 'dias_estimados' => 2, 'habitacion_id' => null, 'ocupacion_id' => null, 'diagnostico' => '22', 'motivo' => '22', 'created_at' => '2026-09-21 04:33:00', 'updated_at' => '2026-09-21 04:33:00'],
            ['id' => 5, 'consentimiento_id' => 10, 'tipo' => 'urgencias', 'prioridad' => 'Crítica', 'fecha_ingreso' => '2026-09-24', 'dias_estimados' => 4, 'habitacion_id' => null, 'ocupacion_id' => null, 'diagnostico' => 'desmayo', 'motivo' => 'entrada del paciente por falta de reposo', 'created_at' => '2026-09-25 04:29:29', 'updated_at' => '2026-09-25 04:29:29'],
            ['id' => 6, 'consentimiento_id' => 11, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-25', 'dias_estimados' => 5, 'habitacion_id' => 1, 'ocupacion_id' => 3, 'diagnostico' => 'entrada del paciente', 'motivo' => 'entrada por revicion del paciente', 'created_at' => '2026-09-25 09:33:18', 'updated_at' => '2026-09-25 09:33:18'],
            ['id' => 7, 'consentimiento_id' => 12, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-25', 'dias_estimados' => 3, 'habitacion_id' => 1, 'ocupacion_id' => 4, 'diagnostico' => 'paciente en observación', 'motivo' => 'este paciente tendra para dejar el alcohol', 'created_at' => '2026-09-25 10:57:20', 'updated_at' => '2026-09-25 10:57:20'],
            ['id' => 8, 'consentimiento_id' => 13, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-09-25', 'dias_estimados' => 5, 'habitacion_id' => 1, 'ocupacion_id' => 6, 'diagnostico' => 'estancia del paciente', 'motivo' => 'dolor de cabeza, cansacio', 'created_at' => '2026-09-26 06:29:19', 'updated_at' => '2026-09-26 06:29:19'],
            ['id' => 9, 'consentimiento_id' => 14, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-10-02', 'dias_estimados' => 4, 'habitacion_id' => 3, 'ocupacion_id' => 7, 'diagnostico' => 'dolor', 'motivo' => 'dolor', 'created_at' => '2026-10-02 06:57:44', 'updated_at' => '2026-10-02 06:57:44'],
            ['id' => 10, 'consentimiento_id' => 15, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-10-09', 'dias_estimados' => 3, 'habitacion_id' => 3, 'ocupacion_id' => 8, 'diagnostico' => 'seguimiento', 'motivo' => 'seguimiento', 'created_at' => '2026-10-10 07:42:34', 'updated_at' => '2026-10-10 07:42:34'],
            ['id' => 11, 'consentimiento_id' => 16, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-10-16', 'dias_estimados' => null, 'habitacion_id' => 1, 'ocupacion_id' => 10, 'diagnostico' => 'knxknwkn', 'motivo' => 'kcwkcolk', 'created_at' => '2026-10-16 23:50:30', 'updated_at' => '2026-10-16 23:50:30'],
            ['id' => 12, 'consentimiento_id' => 17, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-10-16', 'dias_estimados' => 5, 'habitacion_id' => 3, 'ocupacion_id' => 11, 'diagnostico' => 'wedefef', 'motivo' => 'efefefef', 'created_at' => '2026-10-17 00:32:38', 'updated_at' => '2026-10-17 00:32:38'],
            ['id' => 13, 'consentimiento_id' => 18, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-11-09', 'dias_estimados' => 5, 'habitacion_id' => 3, 'ocupacion_id' => 12, 'diagnostico' => 'dolor', 'motivo' => 'dolor', 'created_at' => '2026-11-10 01:14:57', 'updated_at' => '2026-11-10 01:14:57'],
            ['id' => 14, 'consentimiento_id' => 19, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-11-13', 'dias_estimados' => 5, 'habitacion_id' => 3, 'ocupacion_id' => 13, 'diagnostico' => 'estancia del paciente', 'motivo' => 'falta de sueño y estrés', 'created_at' => '2026-11-14 00:12:47', 'updated_at' => '2026-11-14 00:12:47'],
            ['id' => 15, 'consentimiento_id' => 20, 'tipo' => 'general', 'prioridad' => 'Programada', 'fecha_ingreso' => '2026-11-13', 'dias_estimados' => 5, 'habitacion_id' => 1, 'ocupacion_id' => 14, 'diagnostico' => 'vigilar al paciente por lesiones graves', 'motivo' => 'lesiones', 'created_at' => '2026-11-14 00:40:58', 'updated_at' => '2026-11-14 00:40:58'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('consentimiento_hospitalizacion')->insert($chunk);
        }
    }
}
