<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsentimientoFirmasSeeder extends Seeder
{
    /**
     * Datos de la tabla `consentimiento_firmas` (27 registros).
     */
    public function run(): void
    {
        // Placeholder: PNG transparente de 1x1 px (la firma original era una imagen grande).
       $png = '\\x' . bin2hex(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));

        $rows = [
            ['id' => 1, 'consentimiento_id' => 6, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 18:27:52', 'created_at' => '2026-09-21 00:27:52', 'updated_at' => '2026-09-21 00:27:52'],
            ['id' => 2, 'consentimiento_id' => 7, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 18:30:52', 'created_at' => '2026-09-21 00:30:52', 'updated_at' => '2026-09-21 00:30:52'],
            ['id' => 3, 'consentimiento_id' => 7, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 18:31:03', 'created_at' => '2026-09-21 00:31:03', 'updated_at' => '2026-09-21 00:31:03'],
            ['id' => 4, 'consentimiento_id' => 8, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 22:28:52', 'created_at' => '2026-09-21 04:28:52', 'updated_at' => '2026-09-21 04:28:52'],
            ['id' => 5, 'consentimiento_id' => 8, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 22:29:14', 'created_at' => '2026-09-21 04:29:14', 'updated_at' => '2026-09-21 04:29:14'],
            ['id' => 6, 'consentimiento_id' => 9, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 22:33:00', 'created_at' => '2026-09-21 04:33:00', 'updated_at' => '2026-09-21 04:33:00'],
            ['id' => 7, 'consentimiento_id' => 9, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-20 22:33:00', 'created_at' => '2026-09-21 04:33:00', 'updated_at' => '2026-09-21 04:33:00'],
            ['id' => 8, 'consentimiento_id' => 10, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-24 22:29:29', 'created_at' => '2026-09-25 04:29:29', 'updated_at' => '2026-09-25 04:29:29'],
            ['id' => 9, 'consentimiento_id' => 10, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-24 22:29:57', 'created_at' => '2026-09-25 04:29:57', 'updated_at' => '2026-09-25 04:29:57'],
            ['id' => 10, 'consentimiento_id' => 11, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-25 03:33:18', 'created_at' => '2026-09-25 09:33:18', 'updated_at' => '2026-09-25 09:33:18'],
            ['id' => 11, 'consentimiento_id' => 11, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-25 03:33:27', 'created_at' => '2026-09-25 09:33:27', 'updated_at' => '2026-09-25 09:33:27'],
            ['id' => 12, 'consentimiento_id' => 12, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-25 04:57:28', 'created_at' => '2026-09-25 10:57:28', 'updated_at' => '2026-09-25 10:57:28'],
            ['id' => 13, 'consentimiento_id' => 12, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-25 04:57:38', 'created_at' => '2026-09-25 10:57:38', 'updated_at' => '2026-09-25 10:57:38'],
            ['id' => 14, 'consentimiento_id' => 13, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-26 00:29:19', 'created_at' => '2026-09-26 06:29:19', 'updated_at' => '2026-09-26 06:29:19'],
            ['id' => 15, 'consentimiento_id' => 13, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-09-26 00:29:32', 'created_at' => '2026-09-26 06:29:32', 'updated_at' => '2026-09-26 06:29:32'],
            ['id' => 16, 'consentimiento_id' => 14, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-10-02 00:57:54', 'created_at' => '2026-10-02 06:57:54', 'updated_at' => '2026-10-02 06:57:54'],
            ['id' => 17, 'consentimiento_id' => 15, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-10-10 01:42:34', 'created_at' => '2026-10-10 07:42:34', 'updated_at' => '2026-10-10 07:42:34'],
            ['id' => 18, 'consentimiento_id' => 15, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-10-10 01:42:44', 'created_at' => '2026-10-10 07:42:44', 'updated_at' => '2026-10-10 07:42:44'],
            ['id' => 19, 'consentimiento_id' => 17, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-10-16 18:32:38', 'created_at' => '2026-10-17 00:32:38', 'updated_at' => '2026-10-17 00:32:38'],
            ['id' => 20, 'consentimiento_id' => 17, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-10-16 18:32:38', 'created_at' => '2026-10-17 00:32:38', 'updated_at' => '2026-10-17 00:32:38'],
            ['id' => 21, 'consentimiento_id' => 18, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-09 19:14:57', 'created_at' => '2026-11-10 01:14:57', 'updated_at' => '2026-11-10 01:14:57'],
            ['id' => 22, 'consentimiento_id' => 18, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-09 19:14:57', 'created_at' => '2026-11-10 01:14:57', 'updated_at' => '2026-11-10 01:14:57'],
            ['id' => 23, 'consentimiento_id' => 18, 'tipo' => 'Paciente', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-09 19:15:37', 'created_at' => '2026-11-10 01:15:37', 'updated_at' => '2026-11-10 01:15:37'],
            ['id' => 24, 'consentimiento_id' => 19, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-13 18:12:47', 'created_at' => '2026-11-14 00:12:47', 'updated_at' => '2026-11-14 00:12:47'],
            ['id' => 25, 'consentimiento_id' => 19, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-13 18:13:12', 'created_at' => '2026-11-14 00:13:12', 'updated_at' => '2026-11-14 00:13:12'],
            ['id' => 26, 'consentimiento_id' => 20, 'tipo' => 'Clinica', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-13 18:40:58', 'created_at' => '2026-11-14 00:40:58', 'updated_at' => '2026-11-14 00:40:58'],
            ['id' => 27, 'consentimiento_id' => 20, 'tipo' => 'Doctor', 'firma' => $png, 'mime' => 'image/png', 'firmado_en' => '2026-11-13 18:41:10', 'created_at' => '2026-11-14 00:41:10', 'updated_at' => '2026-11-14 00:41:10'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('consentimiento_firmas')->insert($chunk);
        }
    }
}
