<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('habitaciones')->insert([
    ['id' => 1, 'clinica_id' => 6, 'numero' => '1', 'piso' => '1', 'tipo' => 'Compartida', 'estado' => 'Disponible', 'descripcion' => '11111111', 'created_at' => '2026-06-21 04:40:28', 'updated_at' => '2026-06-21 04:40:28'],
]);

    }
}
