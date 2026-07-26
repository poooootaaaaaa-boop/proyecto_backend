<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte_Habitacion;

class ReporteHabitacionController extends Controller
{
    public function postApiAddRegistro(Request $request) {
    // Obtenemos los parámetros de la petición
    $data = $request->all();
    // Establecemos un nombre para la foto
    $ruta_archivo_original = null;

    // Instanciamos un objeto Reporte_Habitacion (o el nombre de tu Modelo)
    // Nota: Asegúrate de tener este Modelo creado, por ejemplo: php artisan make:model Reporte_Habitacion
    $registro = new Reporte_Habitacion();

    // Validamos si la foto se está enviando
    if ($request->hasFile('foto')) {
        // Generamos un nombre único usando el tiempo y concatenamos la extensión de la foto
        $nombreFoto = time().'.'.$request->foto->extension();
        // Movemos el archivo a la carpeta pública con el nombre nuevo
        $request->foto->move(public_path('imagenes_registros'), $nombreFoto);
        // Asignamos el nombre del archivo
        $ruta_archivo_original = $nombreFoto;
    }

    // Se asignan los parámetros de la petición al objeto usando tus campos
    // Cambia esto si en tu BD se llama diferente:
    //$registro->habitacion_id = $data['cuarto_id'];
    $registro->cuarto_id = $data['cuarto_id'];
    $registro->instrumento_id = $data['instrumento_id'];
    $registro->descripcion = $data['descripcion'];

    if ($request->hasFile('foto')) {
        $registro->foto = $ruta_archivo_original;
    }

    // Se ejecuta el método save para agregar el registro en la base de datos
    $registro->save();

    // Opcional: Retornamos una respuesta JSON para que Axios sepa que todo salió bien
    return response()->json([
        'mensaje' => 'Registro guardado con éxito',
        'registro' => $registro
    ], 200);
}
}
