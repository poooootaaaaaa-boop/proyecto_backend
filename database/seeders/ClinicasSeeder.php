<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicasSeeder extends Seeder
{
    /**
     * Datos de la tabla `clinicas` (14 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'usuario_id' => 1, 'nombre' => 'Angeles', 'direccion' => 'calle 56 #666 x 93 y 95 centro', 'ciudad' => 'Mérida', 'estado' => 'Yucatan', 'pais' => 'Mexico', 'telefono' => '9992223456', 'creado_en' => null],
            ['id' => 2, 'usuario_id' => 8, 'nombre' => 'Clínica Abshire Inc', 'direccion' => "2119 Kevon Plains\nNew Wileyfort, CT 05024", 'ciudad' => 'Lake Emelia', 'estado' => 'Montana', 'pais' => 'México', 'telefono' => '1-682-913-3150', 'creado_en' => null],
            ['id' => 3, 'usuario_id' => 10, 'nombre' => 'Clínica Johns, Ortiz and Dare', 'direccion' => "2971 Walsh Trafficway\nWintheiserland, DE 28557", 'ciudad' => 'Agustinashire', 'estado' => 'New Jersey', 'pais' => 'México', 'telefono' => '+1.724.912.5195', 'creado_en' => null],
            ['id' => 4, 'usuario_id' => 12, 'nombre' => 'Clínica Maggio Group', 'direccion' => "5471 Murray Locks\nNorth Nadia, OH 73486-6646", 'ciudad' => 'Gorczanyton', 'estado' => 'Missouri', 'pais' => 'México', 'telefono' => '361-362-2987', 'creado_en' => null],
            ['id' => 5, 'usuario_id' => 18, 'nombre' => 'Clínica Yost, Wilkinson and Keebler', 'direccion' => "252 Julio Burgs\nSouth Britney, ME 19077-0439", 'ciudad' => 'Port Justinaburgh', 'estado' => 'Connecticut', 'pais' => 'México', 'telefono' => '959.341.6580', 'creado_en' => null],
            ['id' => 6, 'usuario_id' => 20, 'nombre' => 'Clínica Wintheiser, Bogisich and Frami', 'direccion' => "147 Balistreri Spring\nSchoenstad, MO 38671", 'ciudad' => 'South Dejatown', 'estado' => 'Nebraska', 'pais' => 'México', 'telefono' => '(202) 255-3075', 'creado_en' => null],
            ['id' => 7, 'usuario_id' => 22, 'nombre' => 'Clínica Streich LLC', 'direccion' => "43937 Allan Terrace Apt. 146\nEast Gregory, NJ 58165", 'ciudad' => 'New Murielbury', 'estado' => 'Idaho', 'pais' => 'México', 'telefono' => '1-757-849-4041', 'creado_en' => null],
            ['id' => 8, 'usuario_id' => 84, 'nombre' => 'Clínica Trantow Group', 'direccion' => "104 Kaylin Brook Apt. 430\nKarleyland, OK 48280-1757", 'ciudad' => 'Lake Rickton', 'estado' => 'Georgia', 'pais' => 'México', 'telefono' => '+1-251-779-1857', 'creado_en' => null],
            ['id' => 9, 'usuario_id' => 86, 'nombre' => 'Clínica Purdy LLC', 'direccion' => "80198 Katarina Summit\nNorth Carlo, WA 85759", 'ciudad' => 'New Irwin', 'estado' => 'Indiana', 'pais' => 'México', 'telefono' => '(786) 261-8898', 'creado_en' => null],
            ['id' => 10, 'usuario_id' => 88, 'nombre' => 'Clínica Emard, Hyatt and Bahringer', 'direccion' => "985 Luettgen Hill Apt. 294\nGennarohaven, DE 70017", 'ciudad' => 'Lennyborough', 'estado' => 'Idaho', 'pais' => 'México', 'telefono' => '+12029172962', 'creado_en' => null],
            ['id' => 11, 'usuario_id' => 153, 'nombre' => 'Issac', 'direccion' => 'calle 56 #675 X 93 y 95', 'ciudad' => 'Mérida', 'estado' => 'Yucatan', 'pais' => 'Mexico', 'telefono' => '9994158594', 'creado_en' => null],
            ['id' => 12, 'usuario_id' => 154, 'nombre' => 'Angel Gabriel', 'direccion' => 'calle 56 #666 93 y 95', 'ciudad' => 'Merida', 'estado' => 'Yucatan', 'pais' => 'Mexico', 'telefono' => '8888888888', 'creado_en' => null],
            ['id' => 13, 'usuario_id' => 157, 'nombre' => 'salus', 'direccion' => 'calle 56 #666 x 93 y 95 centro', 'ciudad' => 'Mérida', 'estado' => 'Yucatan', 'pais' => 'Mexico', 'telefono' => '9992223456', 'creado_en' => null],
            ['id' => 14, 'usuario_id' => 159, 'nombre' => 'salud digna', 'direccion' => 'calle 56 #666 x 93 y 95 centro', 'ciudad' => 'Mérida', 'estado' => 'Yucatan', 'pais' => 'Mexico', 'telefono' => '9992223456', 'creado_en' => null],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('clinicas')->insert($chunk);
        }
    }
}
