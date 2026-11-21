<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpedienteArchivosSeeder extends Seeder
{
    /**
     * Datos de la tabla `expediente_archivos` (1 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'paciente_id' => 106, 'consulta_id' => null, 'nombre_archivo' => 'Actualizaciones en la presentaci┬ón de informaci┬ón referente a casos de COVID.pdf', 'url_archivo' => 'expedientes/106/GvBLzmJHfnvvRqbEGpvOSeUHlLmXmV6mb70AW6h2.pdf', 'tipo' => 'pdf', 'creado_en' => '2026-07-19 07:13:09'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('expediente_archivos')->insert($chunk);
        }
    }
}
