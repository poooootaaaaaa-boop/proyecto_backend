<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsentimientoPdfsSeeder extends Seeder
{
    /**
     * Datos de la tabla `consentimiento_pdfs` (15 registros).
     */
    public function run(): void
    {
        // Placeholder: PDF minimo valido (el original pesaba varios MB).
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";

        $rows = [
            ['id' => 1, 'consentimiento_id' => 7, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 929107, 'generado_en' => '2026-09-20 18:32:00', 'created_at' => '2026-09-21 00:32:00', 'updated_at' => '2026-09-21 00:32:00'],
            ['id' => 2, 'consentimiento_id' => 8, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 949435, 'generado_en' => '2026-09-20 22:29:27', 'created_at' => '2026-09-21 04:29:27', 'updated_at' => '2026-09-21 04:29:27'],
            ['id' => 3, 'consentimiento_id' => 9, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 949384, 'generado_en' => '2026-09-25 04:07:21', 'created_at' => '2026-09-25 10:07:21', 'updated_at' => '2026-09-25 10:07:21'],
            ['id' => 4, 'consentimiento_id' => 10, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 930893, 'generado_en' => '2026-09-26 00:05:35', 'created_at' => '2026-09-26 06:05:35', 'updated_at' => '2026-09-26 06:05:35'],
            ['id' => 5, 'consentimiento_id' => 11, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 932290, 'generado_en' => '2026-09-25 03:34:20', 'created_at' => '2026-09-25 09:34:20', 'updated_at' => '2026-09-25 09:34:20'],
            ['id' => 6, 'consentimiento_id' => 1, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 878748, 'generado_en' => '2026-09-25 04:07:13', 'created_at' => '2026-09-25 10:07:13', 'updated_at' => '2026-09-25 10:07:13'],
            ['id' => 7, 'consentimiento_id' => 12, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 939970, 'generado_en' => '2026-09-25 05:48:01', 'created_at' => '2026-09-25 11:48:01', 'updated_at' => '2026-09-25 11:48:01'],
            ['id' => 8, 'consentimiento_id' => 13, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 949877, 'generado_en' => '2026-09-26 00:29:55', 'created_at' => '2026-09-26 06:29:55', 'updated_at' => '2026-09-26 06:29:55'],
            ['id' => 9, 'consentimiento_id' => 14, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 899716, 'generado_en' => '2026-10-02 00:58:20', 'created_at' => '2026-10-02 06:58:20', 'updated_at' => '2026-10-02 06:58:20'],
            ['id' => 10, 'consentimiento_id' => 15, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 929531, 'generado_en' => '2026-10-10 01:43:07', 'created_at' => '2026-10-10 07:43:07', 'updated_at' => '2026-10-10 07:43:07'],
            ['id' => 11, 'consentimiento_id' => 16, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 879162, 'generado_en' => '2026-10-16 17:51:21', 'created_at' => '2026-10-16 23:51:21', 'updated_at' => '2026-10-16 23:51:21'],
            ['id' => 12, 'consentimiento_id' => 17, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 939959, 'generado_en' => '2026-10-16 18:32:52', 'created_at' => '2026-10-17 00:32:52', 'updated_at' => '2026-10-17 00:32:52'],
            ['id' => 13, 'consentimiento_id' => 18, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 954376, 'generado_en' => '2026-11-09 19:15:45', 'created_at' => '2026-11-10 01:15:45', 'updated_at' => '2026-11-10 01:15:45'],
            ['id' => 14, 'consentimiento_id' => 19, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 929377, 'generado_en' => '2026-11-13 18:13:28', 'created_at' => '2026-11-14 00:13:28', 'updated_at' => '2026-11-14 00:13:28'],
            ['id' => 15, 'consentimiento_id' => 20, 'contenido' => $pdf, 'mime' => 'application/pdf', 'tamano' => 924505, 'generado_en' => '2026-11-13 19:57:14', 'created_at' => '2026-11-14 01:57:14', 'updated_at' => '2026-11-14 01:57:14'],
        ];

        foreach ($rows as &$r) { $r['tamano'] = strlen($r['contenido']); }
        unset($r);

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('consentimiento_pdfs')->insert($chunk);
        }
    }
}
