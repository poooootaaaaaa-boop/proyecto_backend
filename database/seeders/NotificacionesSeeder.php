<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacionesSeeder extends Seeder
{
    /**
     * Datos de la tabla `notificaciones` (2 registros).
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'nombre' => 'Notificaciones por WhatsApp', 'descripcion' => 'Recibir mensajes vía WhatsApp', 'canal' => 'whatsapp', 'activo' => 1],
            ['id' => 2, 'nombre' => 'Notificaciones por Correo', 'descripcion' => 'Recibir mensajes por email', 'canal' => 'email', 'activo' => 1],
        ];

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('notificaciones')->insert($chunk);
        }
    }
}
