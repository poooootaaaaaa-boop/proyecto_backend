<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorConsultoriosSeeder extends Seeder
{
    /**
     * Datos de la tabla `doctor_consultorios` (4 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'doctor_id' => 2, 'consultorio_id' => 1, 'fecha_inicio' => null, 'fecha_fin' => null, 'activo' => 1, 'created_at' => '2026-06-02 06:02:51', 'updated_at' => '2026-06-02 06:02:51'],
            ['id' => 2, 'doctor_id' => 7, 'consultorio_id' => 2, 'fecha_inicio' => null, 'fecha_fin' => null, 'activo' => 1, 'created_at' => '2026-06-02 11:15:56', 'updated_at' => '2026-06-02 11:15:56'],
            ['id' => 3, 'doctor_id' => 13, 'consultorio_id' => 3, 'fecha_inicio' => null, 'fecha_fin' => null, 'activo' => 1, 'created_at' => '2026-06-04 06:20:39', 'updated_at' => '2026-06-04 06:20:39'],
            ['id' => 4, 'doctor_id' => 8, 'consultorio_id' => 2, 'fecha_inicio' => '2026-07-18', 'fecha_fin' => '2026-08-07', 'activo' => 1, 'created_at' => '2026-07-19 06:02:33', 'updated_at' => '2026-07-19 06:02:33'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('doctor_consultorios')->insert($chunk);
        }
    }
}
