<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsentimientoVigilanciaSeeder extends Seeder
{
    /**
     * Datos de la tabla `consentimiento_vigilancia` (38 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'consentimiento_id' => 6, 'doctor_id' => 2, 'created_at' => '2026-09-21 00:06:28'],
            ['id' => 2, 'consentimiento_id' => 6, 'doctor_id' => 1, 'created_at' => '2026-09-21 00:06:28'],
            ['id' => 3, 'consentimiento_id' => 6, 'doctor_id' => 23, 'created_at' => '2026-09-21 00:06:28'],
            ['id' => 4, 'consentimiento_id' => 7, 'doctor_id' => 2, 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 5, 'consentimiento_id' => 7, 'doctor_id' => 1, 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 6, 'consentimiento_id' => 7, 'doctor_id' => 10, 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 7, 'consentimiento_id' => 7, 'doctor_id' => 26, 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 8, 'consentimiento_id' => 7, 'doctor_id' => 29, 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 9, 'consentimiento_id' => 8, 'doctor_id' => 1, 'created_at' => '2026-09-21 04:28:43'],
            ['id' => 10, 'consentimiento_id' => 8, 'doctor_id' => 3, 'created_at' => '2026-09-21 04:28:43'],
            ['id' => 11, 'consentimiento_id' => 8, 'doctor_id' => 21, 'created_at' => '2026-09-21 04:28:43'],
            ['id' => 12, 'consentimiento_id' => 9, 'doctor_id' => 1, 'created_at' => '2026-09-21 04:33:00'],
            ['id' => 13, 'consentimiento_id' => 9, 'doctor_id' => 3, 'created_at' => '2026-09-21 04:33:00'],
            ['id' => 14, 'consentimiento_id' => 10, 'doctor_id' => 27, 'created_at' => '2026-09-25 04:29:29'],
            ['id' => 15, 'consentimiento_id' => 10, 'doctor_id' => 26, 'created_at' => '2026-09-25 04:29:29'],
            ['id' => 16, 'consentimiento_id' => 11, 'doctor_id' => 1, 'created_at' => '2026-09-25 09:33:18'],
            ['id' => 17, 'consentimiento_id' => 11, 'doctor_id' => 23, 'created_at' => '2026-09-25 09:33:18'],
            ['id' => 18, 'consentimiento_id' => 11, 'doctor_id' => 16, 'created_at' => '2026-09-25 09:33:18'],
            ['id' => 19, 'consentimiento_id' => 12, 'doctor_id' => 27, 'created_at' => '2026-09-25 10:57:20'],
            ['id' => 20, 'consentimiento_id' => 12, 'doctor_id' => 26, 'created_at' => '2026-09-25 10:57:20'],
            ['id' => 21, 'consentimiento_id' => 12, 'doctor_id' => 25, 'created_at' => '2026-09-25 10:57:20'],
            ['id' => 22, 'consentimiento_id' => 13, 'doctor_id' => 23, 'created_at' => '2026-09-26 06:29:19'],
            ['id' => 23, 'consentimiento_id' => 13, 'doctor_id' => 28, 'created_at' => '2026-09-26 06:29:19'],
            ['id' => 24, 'consentimiento_id' => 14, 'doctor_id' => 21, 'created_at' => '2026-10-02 06:57:44'],
            ['id' => 25, 'consentimiento_id' => 14, 'doctor_id' => 28, 'created_at' => '2026-10-02 06:57:44'],
            ['id' => 26, 'consentimiento_id' => 15, 'doctor_id' => 28, 'created_at' => '2026-10-10 07:42:34'],
            ['id' => 27, 'consentimiento_id' => 15, 'doctor_id' => 29, 'created_at' => '2026-10-10 07:42:34'],
            ['id' => 28, 'consentimiento_id' => 16, 'doctor_id' => 1, 'created_at' => '2026-10-16 23:50:30'],
            ['id' => 29, 'consentimiento_id' => 16, 'doctor_id' => 12, 'created_at' => '2026-10-16 23:50:30'],
            ['id' => 30, 'consentimiento_id' => 17, 'doctor_id' => 1, 'created_at' => '2026-10-17 00:32:38'],
            ['id' => 31, 'consentimiento_id' => 17, 'doctor_id' => 7, 'created_at' => '2026-10-17 00:32:38'],
            ['id' => 32, 'consentimiento_id' => 18, 'doctor_id' => 6, 'created_at' => '2026-11-10 01:14:57'],
            ['id' => 33, 'consentimiento_id' => 18, 'doctor_id' => 11, 'created_at' => '2026-11-10 01:14:57'],
            ['id' => 34, 'consentimiento_id' => 19, 'doctor_id' => 1, 'created_at' => '2026-11-14 00:12:47'],
            ['id' => 35, 'consentimiento_id' => 19, 'doctor_id' => 30, 'created_at' => '2026-11-14 00:12:47'],
            ['id' => 36, 'consentimiento_id' => 19, 'doctor_id' => 28, 'created_at' => '2026-11-14 00:12:47'],
            ['id' => 37, 'consentimiento_id' => 20, 'doctor_id' => 31, 'created_at' => '2026-11-14 00:40:58'],
            ['id' => 38, 'consentimiento_id' => 20, 'doctor_id' => 27, 'created_at' => '2026-11-14 00:40:58'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('consentimiento_vigilancia')->insert($chunk);
        }
    }
}
