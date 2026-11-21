<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HospitalizacionDocumentosSeeder extends Seeder
{
    /**
     * Datos de la tabla `hospitalizacion_documentos` (29 registros).
     */
    public function run(): void
    {
        // Placeholder: PDF minimo valido (el original pesaba varios MB).
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";

        $rows = [
            ['id' => 1, 'ocupacion_id' => 8, 'habitacion_id' => 4, 'paciente_id' => 6, 'doctor_id' => 15, 'tipo' => 'Alta', 'nombre' => 'Alta hospitalaria - 8', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 22:43:01', 'updated_at' => '2026-10-16 22:43:01'],
            ['id' => 2, 'ocupacion_id' => 8, 'habitacion_id' => 4, 'paciente_id' => 6, 'doctor_id' => 15, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 4', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 22:43:02', 'updated_at' => '2026-10-16 22:43:02'],
            ['id' => 3, 'ocupacion_id' => 6, 'habitacion_id' => 2, 'paciente_id' => 10, 'doctor_id' => 6, 'tipo' => 'Traslado', 'nombre' => 'Traslado hospitalario - 3', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 22:44:23', 'updated_at' => '2026-10-16 22:44:23'],
            ['id' => 4, 'ocupacion_id' => 6, 'habitacion_id' => 1, 'paciente_id' => 10, 'doctor_id' => 6, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 1', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 22:44:23', 'updated_at' => '2026-10-16 22:44:23'],
            ['id' => 5, 'ocupacion_id' => 9, 'habitacion_id' => 3, 'paciente_id' => 6, 'doctor_id' => 12, 'tipo' => 'Ingreso', 'nombre' => 'Ingreso hospitalario - 9', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 22:52:26', 'updated_at' => '2026-10-16 22:52:26'],
            ['id' => 6, 'ocupacion_id' => 10, 'habitacion_id' => 1, 'paciente_id' => 10, 'doctor_id' => 9, 'tipo' => 'Ingreso', 'nombre' => 'Ingreso hospitalario - 10', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 23:50:31', 'updated_at' => '2026-10-16 23:50:31'],
            ['id' => 7, 'ocupacion_id' => 9, 'habitacion_id' => 3, 'paciente_id' => 6, 'doctor_id' => 11, 'tipo' => 'Alta', 'nombre' => 'Alta hospitalaria - 9', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 23:52:45', 'updated_at' => '2026-10-16 23:52:45'],
            ['id' => 8, 'ocupacion_id' => 9, 'habitacion_id' => 3, 'paciente_id' => 6, 'doctor_id' => 11, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 3', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-16 23:52:46', 'updated_at' => '2026-10-16 23:52:46'],
            ['id' => 9, 'ocupacion_id' => 11, 'habitacion_id' => 3, 'paciente_id' => 1, 'doctor_id' => 28, 'tipo' => 'Ingreso', 'nombre' => 'Ingreso hospitalario - 11', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 00:32:39', 'updated_at' => '2026-10-17 00:32:39'],
            ['id' => 10, 'ocupacion_id' => 4, 'habitacion_id' => 1, 'paciente_id' => 2, 'doctor_id' => 28, 'tipo' => 'Reporte estancia', 'nombre' => 'Reporte final de estancia - 4', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 04:04:06', 'updated_at' => '2026-10-17 04:04:06'],
            ['id' => 11, 'ocupacion_id' => 11, 'habitacion_id' => 4, 'paciente_id' => 1, 'doctor_id' => 1, 'tipo' => 'Traslado', 'nombre' => 'Traslado hospitalario - 4', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 04:53:54', 'updated_at' => '2026-10-17 04:53:54'],
            ['id' => 12, 'ocupacion_id' => 11, 'habitacion_id' => 3, 'paciente_id' => 1, 'doctor_id' => 1, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 3', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 04:53:55', 'updated_at' => '2026-10-17 04:53:55'],
            ['id' => 13, 'ocupacion_id' => 6, 'habitacion_id' => 2, 'paciente_id' => 10, 'doctor_id' => 1, 'tipo' => 'Alta', 'nombre' => 'Alta hospitalaria - 6', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 04:56:14', 'updated_at' => '2026-10-17 04:56:14'],
            ['id' => 14, 'ocupacion_id' => 6, 'habitacion_id' => 2, 'paciente_id' => 10, 'doctor_id' => 1, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 2', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 04:56:14', 'updated_at' => '2026-10-17 04:56:14'],
            ['id' => 15, 'ocupacion_id' => 6, 'habitacion_id' => 2, 'paciente_id' => 10, 'doctor_id' => 1, 'tipo' => 'Reporte estancia', 'nombre' => 'Reporte final de estancia - 6', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-10-17 04:56:15', 'updated_at' => '2026-10-17 04:56:15'],
            ['id' => 16, 'ocupacion_id' => 12, 'habitacion_id' => 3, 'paciente_id' => 10, 'doctor_id' => 1, 'tipo' => 'Ingreso', 'nombre' => 'Ingreso hospitalario - 12', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-10 01:15:03', 'updated_at' => '2026-11-10 01:15:03'],
            ['id' => 17, 'ocupacion_id' => 12, 'habitacion_id' => 3, 'paciente_id' => 10, 'doctor_id' => 30, 'tipo' => 'Alta', 'nombre' => 'Alta hospitalaria - 12', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 05:00:20', 'updated_at' => '2026-11-13 05:00:20'],
            ['id' => 18, 'ocupacion_id' => 12, 'habitacion_id' => 3, 'paciente_id' => 10, 'doctor_id' => 30, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 3', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 05:00:21', 'updated_at' => '2026-11-13 05:00:21'],
            ['id' => 19, 'ocupacion_id' => 12, 'habitacion_id' => 3, 'paciente_id' => 10, 'doctor_id' => 30, 'tipo' => 'Reporte estancia', 'nombre' => 'Reporte final de estancia - 12', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 05:00:22', 'updated_at' => '2026-11-13 05:00:22'],
            ['id' => 20, 'ocupacion_id' => 10, 'habitacion_id' => 1, 'paciente_id' => 10, 'doctor_id' => 30, 'tipo' => 'Alta', 'nombre' => 'Alta hospitalaria - 10', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 05:03:52', 'updated_at' => '2026-11-13 05:03:52'],
            ['id' => 21, 'ocupacion_id' => 10, 'habitacion_id' => 1, 'paciente_id' => 10, 'doctor_id' => 30, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 1', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 05:03:52', 'updated_at' => '2026-11-13 05:03:52'],
            ['id' => 22, 'ocupacion_id' => 10, 'habitacion_id' => 1, 'paciente_id' => 10, 'doctor_id' => 30, 'tipo' => 'Reporte estancia', 'nombre' => 'Reporte final de estancia - 10', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 05:03:53', 'updated_at' => '2026-11-13 05:03:53'],
            ['id' => 23, 'ocupacion_id' => 11, 'habitacion_id' => 4, 'paciente_id' => 1, 'doctor_id' => 30, 'tipo' => 'Alta', 'nombre' => 'Alta hospitalaria - 11', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 06:26:14', 'updated_at' => '2026-11-13 06:26:14'],
            ['id' => 24, 'ocupacion_id' => 11, 'habitacion_id' => 4, 'paciente_id' => 1, 'doctor_id' => 30, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 4', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 06:26:14', 'updated_at' => '2026-11-13 06:26:14'],
            ['id' => 25, 'ocupacion_id' => 11, 'habitacion_id' => 4, 'paciente_id' => 1, 'doctor_id' => 30, 'tipo' => 'Reporte estancia', 'nombre' => 'Reporte final de estancia - 11', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-13 06:26:15', 'updated_at' => '2026-11-13 06:26:15'],
            ['id' => 26, 'ocupacion_id' => 13, 'habitacion_id' => 3, 'paciente_id' => 109, 'doctor_id' => 31, 'tipo' => 'Ingreso', 'nombre' => 'Ingreso hospitalario - 13', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-14 00:12:56', 'updated_at' => '2026-11-14 00:12:56'],
            ['id' => 27, 'ocupacion_id' => 14, 'habitacion_id' => 1, 'paciente_id' => 108, 'doctor_id' => 30, 'tipo' => 'Ingreso', 'nombre' => 'Ingreso hospitalario - 14', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-14 00:40:58', 'updated_at' => '2026-11-14 00:40:58'],
            ['id' => 28, 'ocupacion_id' => 13, 'habitacion_id' => 2, 'paciente_id' => 109, 'doctor_id' => 31, 'tipo' => 'Traslado', 'nombre' => 'Traslado hospitalario - 5', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-14 00:53:31', 'updated_at' => '2026-11-14 00:53:31'],
            ['id' => 29, 'ocupacion_id' => 13, 'habitacion_id' => 3, 'paciente_id' => 109, 'doctor_id' => 31, 'tipo' => 'Limpieza', 'nombre' => 'Solicitud de limpieza - Habitación 3', 'contenido' => $pdf, 'mime' => 'application/pdf', 'created_at' => '2026-11-14 00:53:31', 'updated_at' => '2026-11-14 00:53:31'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('hospitalizacion_documentos')->insert($chunk);
        }
    }
}
