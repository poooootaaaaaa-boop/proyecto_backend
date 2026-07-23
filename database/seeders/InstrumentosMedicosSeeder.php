<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstrumentosMedicosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('instrumentos_medicos')->insert([
    ['id' => 1, 'clinica_id' => 5, 'nombre' => 'visturi', 'categoria' => 'ulti', 'cantidad' => 1, 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-06-21 04:41:12', 'updated_at' => '2026-06-21 04:41:12'],
    ['id' => 2, 'clinica_id' => 6, 'nombre' => 'Estetoscopio', 'categoria' => 'corazon', 'cantidad' => 2, 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-06-29 03:46:41', 'updated_at' => '2026-06-29 03:46:41'],
    ['id' => 3, 'clinica_id' => 4, 'nombre' => 'Estetoscopio', 'categoria' => 'corazon', 'cantidad' => 2, 'estado' => 'Disponible', 'descripcion' => null, 'created_at' => '2026-06-29 03:46:54', 'updated_at' => '2026-06-29 03:46:54'],
]);

    }
}
