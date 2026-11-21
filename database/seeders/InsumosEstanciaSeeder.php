<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsumosEstanciaSeeder extends Seeder
{
    /**
     * Datos de la tabla `insumos_estancia` (3 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'nombre' => 'para', 'categoria' => 'suero', 'unidad_medida' => 'pieza', 'precio_unitario' => '123.00', 'activo' => 1, 'created_at' => '2026-10-17 00:28:31', 'updated_at' => '2026-10-17 01:01:53'],
            ['id' => 2, 'nombre' => 'Guantes', 'categoria' => 'material_medico', 'unidad_medida' => 'pieza', 'precio_unitario' => '230.00', 'activo' => 1, 'created_at' => '2026-10-17 03:28:32', 'updated_at' => '2026-10-17 03:28:32'],
            ['id' => 3, 'nombre' => 'Algodones', 'categoria' => 'material_medico', 'unidad_medida' => 'pieza', 'precio_unitario' => '12.00', 'activo' => 1, 'created_at' => '2026-10-17 04:59:01', 'updated_at' => '2026-10-17 04:59:01'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('insumos_estancia')->insert($chunk);
        }
    }
}
