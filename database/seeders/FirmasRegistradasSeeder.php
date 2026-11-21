<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FirmasRegistradasSeeder extends Seeder
{
    /**
     * Datos de la tabla `firmas_registradas` (13 registros).
     */
    public function run(): void
    {
        // Placeholder: PNG transparente de 1x1 px (la firma original era una imagen grande).
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        $rows = [
            ['id' => 1, 'tipo' => 'Clinica', 'referencia_id' => 7, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-21 00:27:52', 'updated_at' => '2026-09-21 00:27:52'],
            ['id' => 2, 'tipo' => 'Doctor', 'referencia_id' => 14, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-21 00:31:03', 'updated_at' => '2026-09-21 00:31:03'],
            ['id' => 3, 'tipo' => 'Clinica', 'referencia_id' => 6, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-21 04:28:52', 'updated_at' => '2026-09-21 04:28:52'],
            ['id' => 4, 'tipo' => 'Doctor', 'referencia_id' => 10, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-21 04:29:14', 'updated_at' => '2026-09-21 04:29:14'],
            ['id' => 5, 'tipo' => 'Doctor', 'referencia_id' => 8, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-25 04:29:57', 'updated_at' => '2026-09-25 04:29:57'],
            ['id' => 6, 'tipo' => 'Doctor', 'referencia_id' => 6, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-25 09:33:27', 'updated_at' => '2026-09-25 09:33:27'],
            ['id' => 7, 'tipo' => 'Clinica', 'referencia_id' => 1, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-25 10:57:28', 'updated_at' => '2026-09-25 10:57:28'],
            ['id' => 8, 'tipo' => 'Doctor', 'referencia_id' => 28, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-25 10:57:38', 'updated_at' => '2026-09-25 10:57:38'],
            ['id' => 9, 'tipo' => 'Doctor', 'referencia_id' => 12, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-09-26 06:29:32', 'updated_at' => '2026-09-26 06:29:32'],
            ['id' => 10, 'tipo' => 'Clinica', 'referencia_id' => 2, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-02 06:57:54', 'updated_at' => '2026-10-02 06:57:54'],
            ['id' => 11, 'tipo' => 'Doctor', 'referencia_id' => 1, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-10-10 07:42:44', 'updated_at' => '2026-10-10 07:42:44'],
            ['id' => 12, 'tipo' => 'Doctor', 'referencia_id' => 31, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-11-14 00:13:12', 'updated_at' => '2026-11-14 00:13:12'],
            ['id' => 13, 'tipo' => 'Doctor', 'referencia_id' => 30, 'firma' => $png, 'mime' => 'image/png', 'created_at' => '2026-11-14 00:41:10', 'updated_at' => '2026-11-14 00:41:10'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('firmas_registradas')->insert($chunk);
        }
    }
}
