<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicamentosCaducadosSeeder extends Seeder
{
    /**
     * Datos de la tabla `medicamentos_caducados` (2 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'inventario_id' => 7, 'cantidad' => 12, 'motivo' => 'ya caduco'],
            ['id' => 2, 'inventario_id' => 5, 'cantidad' => 1, 'motivo' => 'caduco'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('medicamentos_caducados')->insert($chunk);
        }
    }
}
