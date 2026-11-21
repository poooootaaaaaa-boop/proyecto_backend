<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsentimientoHistorialSeeder extends Seeder
{
    /**
     * Datos de la tabla `consentimiento_historial` (71 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'consentimiento_id' => 2, 'usuario_id' => 16, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "nuevo".', 'created_at' => '2026-07-19 07:40:11'],
            ['id' => 2, 'consentimiento_id' => 3, 'usuario_id' => 27, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "nuevo".', 'created_at' => '2026-09-19 00:04:18'],
            ['id' => 3, 'consentimiento_id' => 4, 'usuario_id' => 16, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "nuevo".', 'created_at' => '2026-09-20 03:53:18'],
            ['id' => 5, 'consentimiento_id' => 6, 'usuario_id' => 26, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-21 00:06:28'],
            ['id' => 6, 'consentimiento_id' => 6, 'usuario_id' => 26, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 3 doctor(es) de vigilancia', 'created_at' => '2026-09-21 00:06:28'],
            ['id' => 7, 'consentimiento_id' => 6, 'usuario_id' => 26, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-09-21 00:07:10'],
            ['id' => 8, 'consentimiento_id' => 6, 'usuario_id' => 26, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-09-21 00:17:31'],
            ['id' => 9, 'consentimiento_id' => 6, 'usuario_id' => 26, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-09-21 00:23:40'],
            ['id' => 10, 'consentimiento_id' => 6, 'usuario_id' => 26, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-09-21 00:27:52'],
            ['id' => 11, 'consentimiento_id' => 7, 'usuario_id' => 32, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 12, 'consentimiento_id' => 7, 'usuario_id' => 32, 'accion' => 'Hospitalización', 'descripcion' => 'Obstetricia (Programada) · 5 doctor(es) de vigilancia', 'created_at' => '2026-09-21 00:30:52'],
            ['id' => 13, 'consentimiento_id' => 7, 'usuario_id' => 32, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-09-21 00:31:03'],
            ['id' => 14, 'consentimiento_id' => 7, 'usuario_id' => 32, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-21 00:31:05'],
            ['id' => 15, 'consentimiento_id' => 8, 'usuario_id' => 28, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-21 04:28:43'],
            ['id' => 16, 'consentimiento_id' => 8, 'usuario_id' => 28, 'accion' => 'Hospitalización', 'descripcion' => 'Observación en urgencias (Programada) · 3 doctor(es) de vigilancia', 'created_at' => '2026-09-21 04:28:43'],
            ['id' => 17, 'consentimiento_id' => 8, 'usuario_id' => 28, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-09-21 04:28:54'],
            ['id' => 18, 'consentimiento_id' => 8, 'usuario_id' => 28, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-21 04:29:05'],
            ['id' => 19, 'consentimiento_id' => 8, 'usuario_id' => 28, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-09-21 04:29:14'],
            ['id' => 20, 'consentimiento_id' => 8, 'usuario_id' => 28, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-21 04:29:17'],
            ['id' => 21, 'consentimiento_id' => 9, 'usuario_id' => 28, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-21 04:33:00'],
            ['id' => 22, 'consentimiento_id' => 9, 'usuario_id' => 28, 'accion' => 'Hospitalización', 'descripcion' => 'Observación en urgencias (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-09-21 04:33:00'],
            ['id' => 23, 'consentimiento_id' => 10, 'usuario_id' => 26, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-25 04:29:29'],
            ['id' => 24, 'consentimiento_id' => 10, 'usuario_id' => 26, 'accion' => 'Hospitalización', 'descripcion' => 'Observación en urgencias (Crítica) · 2 doctor(es) de vigilancia', 'created_at' => '2026-09-25 04:29:29'],
            ['id' => 25, 'consentimiento_id' => 10, 'usuario_id' => 26, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-09-25 04:30:02'],
            ['id' => 26, 'consentimiento_id' => 10, 'usuario_id' => 26, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-25 04:30:09'],
            ['id' => 27, 'consentimiento_id' => 11, 'usuario_id' => 24, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-25 09:33:18'],
            ['id' => 28, 'consentimiento_id' => 11, 'usuario_id' => 24, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 3 doctor(es) de vigilancia', 'created_at' => '2026-09-25 09:33:18'],
            ['id' => 29, 'consentimiento_id' => 11, 'usuario_id' => 24, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-09-25 09:33:29'],
            ['id' => 30, 'consentimiento_id' => 11, 'usuario_id' => 24, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-25 09:33:35'],
            ['id' => 31, 'consentimiento_id' => 11, 'usuario_id' => 27, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-09-25 10:48:05'],
            ['id' => 32, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-25 10:57:20'],
            ['id' => 33, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 3 doctor(es) de vigilancia', 'created_at' => '2026-09-25 10:57:20'],
            ['id' => 34, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-09-25 10:57:29'],
            ['id' => 35, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-25 10:57:30'],
            ['id' => 36, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-09-25 10:57:38'],
            ['id' => 37, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-25 10:57:39'],
            ['id' => 38, 'consentimiento_id' => 12, 'usuario_id' => 160, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-09-26 06:00:56'],
            ['id' => 39, 'consentimiento_id' => 13, 'usuario_id' => 30, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-09-26 06:29:19'],
            ['id' => 40, 'consentimiento_id' => 13, 'usuario_id' => 30, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-09-26 06:29:19'],
            ['id' => 41, 'consentimiento_id' => 13, 'usuario_id' => 30, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-09-26 06:29:34'],
            ['id' => 42, 'consentimiento_id' => 13, 'usuario_id' => 30, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-09-26 06:29:37'],
            ['id' => 43, 'consentimiento_id' => 14, 'usuario_id' => 15, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-10-02 06:57:44'],
            ['id' => 44, 'consentimiento_id' => 14, 'usuario_id' => 15, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-10-02 06:57:44'],
            ['id' => 45, 'consentimiento_id' => 14, 'usuario_id' => 15, 'accion' => 'Firma de la clínica', 'descripcion' => 'Se guardó la firma de: clinica.', 'created_at' => '2026-10-02 06:57:58'],
            ['id' => 46, 'consentimiento_id' => 14, 'usuario_id' => 15, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-10-02 06:58:06'],
            ['id' => 47, 'consentimiento_id' => 14, 'usuario_id' => 27, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-10-10 04:52:23'],
            ['id' => 48, 'consentimiento_id' => 15, 'usuario_id' => 2, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-10-10 07:42:34'],
            ['id' => 49, 'consentimiento_id' => 15, 'usuario_id' => 2, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-10-10 07:42:34'],
            ['id' => 50, 'consentimiento_id' => 15, 'usuario_id' => 2, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-10-10 07:42:48'],
            ['id' => 51, 'consentimiento_id' => 15, 'usuario_id' => 2, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-10-10 07:42:54'],
            ['id' => 52, 'consentimiento_id' => 15, 'usuario_id' => 33, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-10-16 22:43:02'],
            ['id' => 53, 'consentimiento_id' => 16, 'usuario_id' => 27, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-10-16 23:50:30'],
            ['id' => 54, 'consentimiento_id' => 16, 'usuario_id' => 27, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-10-16 23:50:31'],
            ['id' => 55, 'consentimiento_id' => 17, 'usuario_id' => 160, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-10-17 00:32:38'],
            ['id' => 56, 'consentimiento_id' => 17, 'usuario_id' => 160, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-10-17 00:32:39'],
            ['id' => 57, 'consentimiento_id' => 13, 'usuario_id' => 2, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-10-17 04:56:14'],
            ['id' => 58, 'consentimiento_id' => 18, 'usuario_id' => 2, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-11-10 01:14:57'],
            ['id' => 59, 'consentimiento_id' => 18, 'usuario_id' => 2, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-11-10 01:15:04'],
            ['id' => 60, 'consentimiento_id' => 18, 'usuario_id' => 2, 'accion' => 'Firmado', 'descripcion' => 'Se guardó la firma de: paciente.', 'created_at' => '2026-11-10 01:15:38'],
            ['id' => 61, 'consentimiento_id' => 18, 'usuario_id' => 2, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-11-10 01:15:39'],
            ['id' => 62, 'consentimiento_id' => 18, 'usuario_id' => 165, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-11-13 05:00:21'],
            ['id' => 63, 'consentimiento_id' => 16, 'usuario_id' => 165, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-11-13 05:03:52'],
            ['id' => 64, 'consentimiento_id' => 17, 'usuario_id' => 165, 'accion' => 'Finalizado automáticamente', 'descripcion' => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.', 'created_at' => '2026-11-13 06:26:14'],
            ['id' => 65, 'consentimiento_id' => 19, 'usuario_id' => 166, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-11-14 00:12:47'],
            ['id' => 66, 'consentimiento_id' => 19, 'usuario_id' => 166, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 3 doctor(es) de vigilancia', 'created_at' => '2026-11-14 00:12:57'],
            ['id' => 67, 'consentimiento_id' => 19, 'usuario_id' => 166, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-11-14 00:13:14'],
            ['id' => 68, 'consentimiento_id' => 19, 'usuario_id' => 166, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-11-14 00:13:16'],
            ['id' => 69, 'consentimiento_id' => 20, 'usuario_id' => 165, 'accion' => 'Creado', 'descripcion' => 'Se generó el consentimiento a partir del formato "estancia de vigilacion".', 'created_at' => '2026-11-14 00:40:58'],
            ['id' => 70, 'consentimiento_id' => 20, 'usuario_id' => 165, 'accion' => 'Hospitalización', 'descripcion' => 'Hospitalización general (Programada) · 2 doctor(es) de vigilancia', 'created_at' => '2026-11-14 00:40:58'],
            ['id' => 71, 'consentimiento_id' => 20, 'usuario_id' => 165, 'accion' => 'Firma del doctor', 'descripcion' => 'Se guardó la firma de: doctor.', 'created_at' => '2026-11-14 00:41:10'],
            ['id' => 72, 'consentimiento_id' => 20, 'usuario_id' => 165, 'accion' => 'PDF generado', 'descripcion' => 'Se generó el documento PDF con las firmas actuales.', 'created_at' => '2026-11-14 00:41:12'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('consentimiento_historial')->insert($chunk);
        }
    }
}
