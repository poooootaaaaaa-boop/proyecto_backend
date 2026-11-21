<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SesionesDoctoresSeeder extends Seeder
{
    /**
     * Datos de la tabla `sesiones_doctores` (14 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'usuario_id' => 165, 'token_id' => 137, 'inicio_sesion' => '2026-11-12 02:05:50', 'fin_sesion' => '2026-11-12 02:06:14', 'estado' => 'cerrada', 'created_at' => '2026-11-12 08:05:50', 'updated_at' => '2026-11-12 08:06:14'],
            ['id' => 2, 'usuario_id' => 165, 'token_id' => 138, 'inicio_sesion' => '2026-11-12 02:07:57', 'fin_sesion' => '2026-11-12 02:08:13', 'estado' => 'cerrada', 'created_at' => '2026-11-12 08:07:57', 'updated_at' => '2026-11-12 08:08:13'],
            ['id' => 3, 'usuario_id' => 165, 'token_id' => 139, 'inicio_sesion' => '2026-11-12 02:23:41', 'fin_sesion' => '2026-11-12 02:23:48', 'estado' => 'cerrada', 'created_at' => '2026-11-12 08:23:41', 'updated_at' => '2026-11-12 08:23:48'],
            ['id' => 4, 'usuario_id' => 165, 'token_id' => 140, 'inicio_sesion' => '2026-11-12 20:11:23', 'fin_sesion' => '2026-11-12 20:11:47', 'estado' => 'cerrada', 'created_at' => '2026-11-13 02:11:23', 'updated_at' => '2026-11-13 02:11:47'],
            ['id' => 5, 'usuario_id' => 165, 'token_id' => 141, 'inicio_sesion' => '2026-11-12 20:25:56', 'fin_sesion' => null, 'estado' => 'activa', 'created_at' => '2026-11-13 02:25:56', 'updated_at' => '2026-11-13 02:25:56'],
            ['id' => 6, 'usuario_id' => 165, 'token_id' => 143, 'inicio_sesion' => '2026-11-12 21:14:17', 'fin_sesion' => '2026-11-12 21:16:49', 'estado' => 'cerrada', 'created_at' => '2026-11-13 03:14:17', 'updated_at' => '2026-11-13 03:16:49'],
            ['id' => 7, 'usuario_id' => 165, 'token_id' => 144, 'inicio_sesion' => '2026-11-12 22:23:02', 'fin_sesion' => '2026-11-12 22:23:10', 'estado' => 'cerrada', 'created_at' => '2026-11-13 04:23:02', 'updated_at' => '2026-11-13 04:23:10'],
            ['id' => 8, 'usuario_id' => 165, 'token_id' => 145, 'inicio_sesion' => '2026-11-12 22:23:34', 'fin_sesion' => '2026-11-12 22:27:42', 'estado' => 'cerrada', 'created_at' => '2026-11-13 04:23:34', 'updated_at' => '2026-11-13 04:27:42'],
            ['id' => 9, 'usuario_id' => 165, 'token_id' => 148, 'inicio_sesion' => '2026-11-12 23:25:44', 'fin_sesion' => '2026-11-12 23:26:14', 'estado' => 'cerrada', 'created_at' => '2026-11-13 05:25:44', 'updated_at' => '2026-11-13 05:26:14'],
            ['id' => 10, 'usuario_id' => 165, 'token_id' => 151, 'inicio_sesion' => '2026-11-13 16:14:13', 'fin_sesion' => '2026-11-13 16:20:59', 'estado' => 'cerrada', 'created_at' => '2026-11-13 22:14:13', 'updated_at' => '2026-11-13 22:20:59'],
            ['id' => 11, 'usuario_id' => 165, 'token_id' => 152, 'inicio_sesion' => '2026-11-13 16:31:34', 'fin_sesion' => '2026-11-13 16:31:45', 'estado' => 'cerrada', 'created_at' => '2026-11-13 22:31:34', 'updated_at' => '2026-11-13 22:31:45'],
            ['id' => 12, 'usuario_id' => 165, 'token_id' => 153, 'inicio_sesion' => '2026-11-13 16:32:19', 'fin_sesion' => '2026-11-13 16:37:24', 'estado' => 'cerrada', 'created_at' => '2026-11-13 22:32:19', 'updated_at' => '2026-11-13 22:37:24'],
            ['id' => 13, 'usuario_id' => 166, 'token_id' => 155, 'inicio_sesion' => '2026-11-13 16:37:32', 'fin_sesion' => '2026-11-13 16:37:55', 'estado' => 'cerrada', 'created_at' => '2026-11-13 22:37:32', 'updated_at' => '2026-11-13 22:37:55'],
            ['id' => 14, 'usuario_id' => 166, 'token_id' => 156, 'inicio_sesion' => '2026-11-13 16:38:40', 'fin_sesion' => null, 'estado' => 'activa', 'created_at' => '2026-11-13 22:38:40', 'updated_at' => '2026-11-13 22:38:40'],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('sesiones_doctores')->insert($chunk);
        }
    }
}
