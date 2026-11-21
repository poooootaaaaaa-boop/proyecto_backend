<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrasladoFirmasSeeder extends Seeder
{
    /**
     * Datos de la tabla `traslado_firmas` (10 registros).
     */
    public function run(): void
    {
        // Placeholder: PNG transparente de 1x1 px (la firma original era una imagen grande).
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        $rows = [
            ['id' => 1, 'traslado_id' => 3, 'tipo' => 'Doctor', 'nombre' => 'Vladimir Tromp', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-16 22:44:21', 'updated_at' => '2026-10-16 22:44:21'],
            ['id' => 2, 'traslado_id' => 3, 'tipo' => 'Tutor', 'nombre' => 'hihihih', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-16 22:44:21', 'updated_at' => '2026-10-16 22:44:21'],
            ['id' => 3, 'traslado_id' => 3, 'tipo' => 'Familiar', 'nombre' => 'hiiihih', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-16 22:44:21', 'updated_at' => '2026-10-16 22:44:21'],
            ['id' => 4, 'traslado_id' => 3, 'tipo' => 'Paciente', 'nombre' => 'Philip Crona', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-16 22:44:21', 'updated_at' => '2026-10-16 22:44:21'],
            ['id' => 5, 'traslado_id' => 3, 'tipo' => 'Clinica', 'nombre' => 'Clínica Streich LLC', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-16 22:44:21', 'updated_at' => '2026-10-16 22:44:21'],
            ['id' => 6, 'traslado_id' => 4, 'tipo' => 'Doctor', 'nombre' => 'Carlos', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-17 04:53:52', 'updated_at' => '2026-10-17 04:53:52'],
            ['id' => 7, 'traslado_id' => 4, 'tipo' => 'Clinica', 'nombre' => 'Angeles', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-17 04:53:52', 'updated_at' => '2026-10-17 04:53:52'],
            ['id' => 8, 'traslado_id' => 5, 'tipo' => 'Doctor', 'nombre' => 'Diego', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-11-14 00:53:28', 'updated_at' => '2026-11-14 00:53:28'],
            ['id' => 9, 'traslado_id' => 5, 'tipo' => 'Clinica', 'nombre' => 'Angeles', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-11-14 00:53:28', 'updated_at' => '2026-11-14 00:53:28'],
            ['id' => 10, 'traslado_id' => 5, 'tipo' => 'Familiar', 'nombre' => 'lorena', 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-11-14 00:53:28', 'updated_at' => '2026-11-14 00:53:28'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('traslado_firmas')->insert($chunk);
        }
    }
}
