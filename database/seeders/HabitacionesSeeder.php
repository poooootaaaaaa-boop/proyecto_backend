<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitacionesSeeder extends Seeder
{
    /**
     * Datos de la tabla `habitaciones` (4 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'clinica_id' => 11, 'numero' => '1', 'piso' => '1', 'tipo' => 'Compartida', 'estado' => 'Ocupada', 'descripcion' => '11111111', 'created_at' => '2026-06-21 04:40:28', 'updated_at' => '2026-11-14 00:40:58'],
            ['id' => 2, 'clinica_id' => 11, 'numero' => '2', 'piso' => '2', 'tipo' => 'Urgencias', 'estado' => 'Ocupada', 'descripcion' => 'esta es una habitacion para hospitalisacion', 'created_at' => '2026-09-25 10:58:45', 'updated_at' => '2026-11-14 00:53:28'],
            ['id' => 3, 'clinica_id' => 11, 'numero' => '3', 'piso' => '2', 'tipo' => 'Compartida', 'estado' => 'Disponible', 'descripcion' => 'esta es una habitacion para hospitalisacion', 'created_at' => '2026-09-25 10:59:08', 'updated_at' => '2026-11-14 00:56:13'],
            ['id' => 4, 'clinica_id' => 11, 'numero' => '4', 'piso' => '2', 'tipo' => 'Compartida', 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-10-10 04:48:31', 'updated_at' => '2026-11-14 00:56:17'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('habitaciones')->insert($chunk);
        }
    }
}
