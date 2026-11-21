<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReporteHabitacionsSeeder extends Seeder
{
    /**
     * Datos de la tabla `reporte__habitacions` (3 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'cuarto_id' => 2, 'instrumento_id' => 2, 'descripcion' => 'efsefsefe', 'foto' => '1791588473.png', 'costo' => '1232.97', 'prioridad' => 'media', 'fecha_registro' => '2026-10-09', 'created_at' => '2026-10-10 05:27:53', 'updated_at' => '2026-10-10 05:27:53'],
            ['id' => 2, 'cuarto_id' => 2, 'instrumento_id' => 2, 'descripcion' => 'falla del instrumento', 'foto' => '1791588916.png', 'costo' => '1229.91', 'prioridad' => 'alta', 'fecha_registro' => '2026-10-09', 'created_at' => '2026-10-10 05:35:16', 'updated_at' => '2026-10-10 05:35:16'],
            ['id' => 3, 'cuarto_id' => 3, 'instrumento_id' => 1, 'descripcion' => 'sdsdsdsd', 'foto' => '1794529002.png', 'costo' => '123.00', 'prioridad' => 'media', 'fecha_registro' => '2026-11-12', 'created_at' => '2026-11-13 06:16:42', 'updated_at' => '2026-11-13 06:16:42'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('reporte__habitacions')->insert($chunk);
        }
    }
}
