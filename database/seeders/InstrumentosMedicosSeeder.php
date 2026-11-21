<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstrumentosMedicosSeeder extends Seeder
{
    /**
     * Datos de la tabla `instrumentos_medicos` (3 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'clinica_id' => 11, 'nombre' => 'visturi', 'categoria' => 'ulti', 'cantidad' => 1, 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-06-21 04:41:12', 'updated_at' => '2026-06-21 04:41:12'],
            ['id' => 2, 'clinica_id' => 11, 'nombre' => 'Estetoscopio', 'categoria' => 'corazon', 'cantidad' => 2, 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-06-29 03:46:41', 'updated_at' => '2026-06-29 03:46:41'],
            ['id' => 3, 'clinica_id' => 11, 'nombre' => 'Estetoscopio', 'categoria' => 'corazon', 'cantidad' => 2, 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-06-29 03:46:54', 'updated_at' => '2026-06-29 03:46:54'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('instrumentos_medicos')->insert($chunk);
        }
    }
}
