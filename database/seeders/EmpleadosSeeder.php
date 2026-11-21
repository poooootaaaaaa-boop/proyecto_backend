<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadosSeeder extends Seeder
{
    /**
     * Datos de la tabla `empleados` (1 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'clinica_id' => 11, 'puesto_id' => 8, 'numero_empleado' => 'EMP-0001', 'nombre' => 'pabo', 'apellidos' => 'perez', 'correo' => 'pabo@gmail.com', 'telefono' => '9999993344', 'fecha_nacimiento' => '2026-10-23', 'curp' => 'PEPA050226HYNRTNA9', 'rfc' => 'LOGJ580812RH7', 'nss' => '12-34-56-78', 'direccion' => 'Calle 56, Distrito 1, Mérida, Yucatán, 97180, México', 'contacto_emergencia_nombre' => '21212121212', 'contacto_emergencia_telefono' => '2121212121', 'fecha_ingreso' => '2026-10-30', 'tipo_contrato' => 'determinado', 'salario_mensual' => '1222222.00', 'estado' => 'activo', 'fecha_baja' => null, 'motivo_baja' => null, 'created_at' => '2026-10-30 12:50:44', 'updated_at' => '2026-10-30 12:50:44'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('empleados')->insert($chunk);
        }
    }
}
