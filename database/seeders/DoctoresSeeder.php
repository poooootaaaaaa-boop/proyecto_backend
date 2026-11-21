<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctoresSeeder extends Seeder
{
    /**
     * Datos de la tabla `doctores` (30 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'usuario_id' => 2, 'clinica_id' => 1, 'especialidad_id' => 1, 'cedula_profesional' => '23456', 'anios_exp' => 23, 'telefono' => '9992223456', 'creado_en' => null],
            ['id' => 2, 'usuario_id' => 14, 'clinica_id' => 3, 'especialidad_id' => 1, 'cedula_profesional' => '641150', 'anios_exp' => 23, 'telefono' => '1-878-761-2738', 'creado_en' => null],
            ['id' => 3, 'usuario_id' => 15, 'clinica_id' => 2, 'especialidad_id' => 1, 'cedula_profesional' => '910029', 'anios_exp' => 4, 'telefono' => '(239) 982-7810', 'creado_en' => null],
            ['id' => 4, 'usuario_id' => 16, 'clinica_id' => 2, 'especialidad_id' => 1, 'cedula_profesional' => '076024', 'anios_exp' => 19, 'telefono' => '+1-947-635-4104', 'creado_en' => null],
            ['id' => 6, 'usuario_id' => 24, 'clinica_id' => 7, 'especialidad_id' => 3, 'cedula_profesional' => '214858', 'anios_exp' => 25, 'telefono' => '+1 (737) 610-9113', 'creado_en' => null],
            ['id' => 7, 'usuario_id' => 25, 'clinica_id' => 7, 'especialidad_id' => 2, 'cedula_profesional' => '108107', 'anios_exp' => 38, 'telefono' => '(978) 435-1412', 'creado_en' => null],
            ['id' => 8, 'usuario_id' => 26, 'clinica_id' => 7, 'especialidad_id' => 3, 'cedula_profesional' => '773679', 'anios_exp' => 30, 'telefono' => '(539) 436-4036', 'creado_en' => null],
            ['id' => 9, 'usuario_id' => 27, 'clinica_id' => 5, 'especialidad_id' => 3, 'cedula_profesional' => '852027', 'anios_exp' => 3, 'telefono' => '+1-641-989-9649', 'creado_en' => null],
            ['id' => 10, 'usuario_id' => 28, 'clinica_id' => 6, 'especialidad_id' => 2, 'cedula_profesional' => '100116', 'anios_exp' => 13, 'telefono' => '1-667-239-5771', 'creado_en' => null],
            ['id' => 11, 'usuario_id' => 29, 'clinica_id' => 6, 'especialidad_id' => 1, 'cedula_profesional' => '667900', 'anios_exp' => 35, 'telefono' => '+1.520.421.3790', 'creado_en' => null],
            ['id' => 12, 'usuario_id' => 30, 'clinica_id' => 6, 'especialidad_id' => 2, 'cedula_profesional' => '807154', 'anios_exp' => 9, 'telefono' => '(385) 558-6495', 'creado_en' => null],
            ['id' => 13, 'usuario_id' => 31, 'clinica_id' => 6, 'especialidad_id' => 1, 'cedula_profesional' => '795820', 'anios_exp' => 27, 'telefono' => '276.247.7033', 'creado_en' => null],
            ['id' => 14, 'usuario_id' => 32, 'clinica_id' => 7, 'especialidad_id' => 3, 'cedula_profesional' => '366420', 'anios_exp' => 2, 'telefono' => '1-267-550-7817', 'creado_en' => null],
            ['id' => 15, 'usuario_id' => 33, 'clinica_id' => 7, 'especialidad_id' => 3, 'cedula_profesional' => '754376', 'anios_exp' => 35, 'telefono' => '615.418.5233', 'creado_en' => null],
            ['id' => 16, 'usuario_id' => 90, 'clinica_id' => 9, 'especialidad_id' => 3, 'cedula_profesional' => '737870', 'anios_exp' => 32, 'telefono' => '+1.573.740.7682', 'creado_en' => null],
            ['id' => 17, 'usuario_id' => 91, 'clinica_id' => 10, 'especialidad_id' => 2, 'cedula_profesional' => '000390', 'anios_exp' => 20, 'telefono' => '+1.530.900.2717', 'creado_en' => null],
            ['id' => 18, 'usuario_id' => 92, 'clinica_id' => 10, 'especialidad_id' => 2, 'cedula_profesional' => '561557', 'anios_exp' => 38, 'telefono' => '936.703.9099', 'creado_en' => null],
            ['id' => 19, 'usuario_id' => 93, 'clinica_id' => 8, 'especialidad_id' => 2, 'cedula_profesional' => '953211', 'anios_exp' => 6, 'telefono' => '+1 (412) 616-2360', 'creado_en' => null],
            ['id' => 20, 'usuario_id' => 94, 'clinica_id' => 9, 'especialidad_id' => 2, 'cedula_profesional' => '815399', 'anios_exp' => 24, 'telefono' => '+1.316.389.4711', 'creado_en' => null],
            ['id' => 21, 'usuario_id' => 95, 'clinica_id' => 10, 'especialidad_id' => 2, 'cedula_profesional' => '732254', 'anios_exp' => 29, 'telefono' => '225-318-2950', 'creado_en' => null],
            ['id' => 22, 'usuario_id' => 96, 'clinica_id' => 8, 'especialidad_id' => 2, 'cedula_profesional' => '616521', 'anios_exp' => 36, 'telefono' => '+13163505239', 'creado_en' => null],
            ['id' => 23, 'usuario_id' => 97, 'clinica_id' => 9, 'especialidad_id' => 3, 'cedula_profesional' => '946464', 'anios_exp' => 38, 'telefono' => '(330) 437-4468', 'creado_en' => null],
            ['id' => 24, 'usuario_id' => 98, 'clinica_id' => 10, 'especialidad_id' => 2, 'cedula_profesional' => '379700', 'anios_exp' => 21, 'telefono' => '+1-562-429-0442', 'creado_en' => null],
            ['id' => 25, 'usuario_id' => 99, 'clinica_id' => 10, 'especialidad_id' => 2, 'cedula_profesional' => '062613', 'anios_exp' => 26, 'telefono' => '717-592-9462', 'creado_en' => null],
            ['id' => 26, 'usuario_id' => 151, 'clinica_id' => 1, 'especialidad_id' => 7, 'cedula_profesional' => '23456', 'anios_exp' => 23, 'telefono' => '9992223456', 'creado_en' => null],
            ['id' => 27, 'usuario_id' => 155, 'clinica_id' => 1, 'especialidad_id' => 1, 'cedula_profesional' => '232323', 'anios_exp' => 2, 'telefono' => '222222222', 'creado_en' => null],
            ['id' => 28, 'usuario_id' => 160, 'clinica_id' => 1, 'especialidad_id' => 7, 'cedula_profesional' => '122222', 'anios_exp' => 2, 'telefono' => '9999993344', 'creado_en' => null],
            ['id' => 29, 'usuario_id' => 162, 'clinica_id' => 1, 'especialidad_id' => 2, 'cedula_profesional' => '33333', 'anios_exp' => 2, 'telefono' => '9991234569', 'creado_en' => null],
            ['id' => 30, 'usuario_id' => 165, 'clinica_id' => 1, 'especialidad_id' => 2, 'cedula_profesional' => '122223', 'anios_exp' => 2, 'telefono' => '9999993344', 'creado_en' => null],
            ['id' => 31, 'usuario_id' => 166, 'clinica_id' => 1, 'especialidad_id' => 2, 'cedula_profesional' => '122224', 'anios_exp' => 5, 'telefono' => '9999993344', 'creado_en' => null],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('doctores')->insert($chunk);
        }
    }
}
