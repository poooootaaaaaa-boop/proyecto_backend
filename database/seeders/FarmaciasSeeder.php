<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FarmaciasSeeder extends Seeder
{
    /**
     * Datos de la tabla `farmacias` (14 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'usuario_id' => 1, 'clinica_id' => 1, 'nombre' => 'Angeles', 'direccion' => 'calle 56 #666 x 93 y 95 centro', 'telefono' => '9992223456', 'creado_en' => null],
            ['id' => 2, 'usuario_id' => 9, 'clinica_id' => 2, 'nombre' => 'Farmacia Turner-Marks', 'direccion' => "651 Magdalena Wall\nHoseatown, MN 20413-0396", 'telefono' => '+1-781-882-9161', 'creado_en' => null],
            ['id' => 3, 'usuario_id' => 11, 'clinica_id' => 3, 'nombre' => 'Farmacia Towne-Mohr', 'direccion' => "9671 Sawayn Hill Apt. 641\nReynoldshaven, TX 08674", 'telefono' => '1-231-966-7673', 'creado_en' => null],
            ['id' => 4, 'usuario_id' => 13, 'clinica_id' => 4, 'nombre' => 'Farmacia Larkin-Hudson', 'direccion' => "481 Jacobson Burg\nLibbieside, AZ 09211", 'telefono' => '1-912-320-0363', 'creado_en' => null],
            ['id' => 5, 'usuario_id' => 19, 'clinica_id' => 5, 'nombre' => 'Farmacia Lubowitz Inc', 'direccion' => "5932 Olson Forest Apt. 093\nCreminfurt, DC 45124-1801", 'telefono' => '+1-360-654-6515', 'creado_en' => null],
            ['id' => 6, 'usuario_id' => 21, 'clinica_id' => 6, 'nombre' => 'Farmacia Gerhold, Rodriguez and Stanton', 'direccion' => "1793 Gaylord Haven\nEast Hallie, CT 55795-9131", 'telefono' => '806-233-2129', 'creado_en' => null],
            ['id' => 7, 'usuario_id' => 23, 'clinica_id' => 7, 'nombre' => 'Farmacia Hagenes, Grady and Mertz', 'direccion' => "45907 O'Keefe Route\nPriscillaside, IL 09545", 'telefono' => '+1-402-380-7507', 'creado_en' => null],
            ['id' => 8, 'usuario_id' => 85, 'clinica_id' => 8, 'nombre' => 'Farmacia Hartmann, Quigley and Zulauf', 'direccion' => "469 Rebecca Court Suite 287\nLindgrenbury, AL 93437", 'telefono' => '(775) 207-8222', 'creado_en' => null],
            ['id' => 9, 'usuario_id' => 87, 'clinica_id' => 9, 'nombre' => 'Farmacia Hamill LLC', 'direccion' => "28959 Gerlach Causeway\nNorth Genovevaville, IL 52351-8584", 'telefono' => '+1 (919) 418-2456', 'creado_en' => null],
            ['id' => 10, 'usuario_id' => 89, 'clinica_id' => 10, 'nombre' => 'Farmacia Rodriguez, Johnston and Cassin', 'direccion' => "442 Orrin Ports Suite 003\nLangoshbury, SD 03054-6649", 'telefono' => '1-630-243-5406', 'creado_en' => null],
            ['id' => 11, 'usuario_id' => 153, 'clinica_id' => 11, 'nombre' => 'Issac', 'direccion' => 'calle 56 #675 X 93 y 95', 'telefono' => '9994158594', 'creado_en' => null],
            ['id' => 12, 'usuario_id' => 154, 'clinica_id' => 12, 'nombre' => 'Angel Gabriel', 'direccion' => 'calle 56 #666 93 y 95', 'telefono' => '8888888888', 'creado_en' => null],
            ['id' => 13, 'usuario_id' => 157, 'clinica_id' => 13, 'nombre' => 'salus', 'direccion' => 'calle 56 #666 x 93 y 95 centro', 'telefono' => '9992223456', 'creado_en' => null],
            ['id' => 14, 'usuario_id' => 159, 'clinica_id' => 14, 'nombre' => 'salud digna', 'direccion' => 'calle 56 #666 x 93 y 95 centro', 'telefono' => '9992223456', 'creado_en' => null],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('farmacias')->insert($chunk);
        }
    }
}
