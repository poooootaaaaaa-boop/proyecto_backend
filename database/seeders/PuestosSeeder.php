<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PuestosSeeder extends Seeder
{
    /**
     * Datos de la tabla `puestos` (8 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'clinica_id' => 1, 'nombre' => 'Enfermero(a)', 'descripcion' => 'Atención y cuidado directo de pacientes', 'salario_base' => null, 'activo' => 1, 'created_at' => '2026-10-30 04:28:20', 'updated_at' => '2026-10-30 04:28:20'],
            ['id' => 2, 'clinica_id' => 1, 'nombre' => 'Recepcionista', 'descripcion' => 'Atención al público, citas y admisiones', 'salario_base' => null, 'activo' => 1, 'created_at' => '2026-10-30 04:28:20', 'updated_at' => '2026-10-30 04:28:20'],
            ['id' => 3, 'clinica_id' => 1, 'nombre' => 'Administrativo(a)', 'descripcion' => 'Gestión administrativa y facturación', 'salario_base' => null, 'activo' => 1, 'created_at' => '2026-10-30 04:28:20', 'updated_at' => '2026-10-30 04:28:20'],
            ['id' => 4, 'clinica_id' => 1, 'nombre' => 'Laboratorista', 'descripcion' => 'Toma y análisis de muestras', 'salario_base' => null, 'activo' => 1, 'created_at' => '2026-10-30 04:28:20', 'updated_at' => '2026-10-30 04:28:20'],
            ['id' => 5, 'clinica_id' => 1, 'nombre' => 'Farmacéutico(a)', 'descripcion' => 'Control y dispensación de medicamentos', 'salario_base' => null, 'activo' => 1, 'created_at' => '2026-10-30 04:28:20', 'updated_at' => '2026-10-30 04:28:20'],
            ['id' => 6, 'clinica_id' => 1, 'nombre' => 'Intendencia', 'descripcion' => 'Limpieza y mantenimiento de instalaciones', 'salario_base' => null, 'activo' => 1, 'created_at' => '2026-10-30 04:28:20', 'updated_at' => '2026-10-30 04:28:20'],
            ['id' => 7, 'clinica_id' => 11, 'nombre' => 'limpieza', 'descripcion' => 'limpiesa', 'salario_base' => '12222.00', 'activo' => 1, 'created_at' => '2026-10-30 12:43:21', 'updated_at' => '2026-10-30 12:43:21'],
            ['id' => 8, 'clinica_id' => 11, 'nombre' => 'enfermero', 'descripcion' => 'enfermero', 'salario_base' => '1222222.00', 'activo' => 1, 'created_at' => '2026-10-30 12:43:57', 'updated_at' => '2026-10-30 12:43:57'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('puestos')->insert($chunk);
        }
    }
}
