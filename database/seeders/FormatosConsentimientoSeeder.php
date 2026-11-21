<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormatosConsentimientoSeeder extends Seeder
{
    /**
     * Datos de la tabla `formatos_consentimiento` (2 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'nombre' => 'nuevo', 'descripcion' => 'caknclkanclknacknasklcnlksnclksnaclknsclknslkcnlksncvlkdnsvcklnsdlkcvnlksdncklsdncklsndcklnsdcknskldncd', 'contenido' => 'csdcknladknck;asnc;kasncknsalkcnlknclkndclknsdlkcnlksnd', 'requiere_firma' => 1, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'nombre' => 'estancia de vigilacion', 'descripcion' => 'estancia de 5 noches en el hospital para revision', 'contenido' => 'yo el doctro eduardo autoriso para que el paciente se quede un total de 5 diads en la clinica de merida par la revicion de del paciente', 'requiere_firma' => 1, 'activo' => 1, 'created_at' => '2026-09-20 23:58:34', 'updated_at' => '2026-09-20 23:58:34'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('formatos_consentimiento')->insert($chunk);
        }
    }
}
