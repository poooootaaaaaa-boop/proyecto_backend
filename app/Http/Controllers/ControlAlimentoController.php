<?php

namespace App\Http\Controllers;

use App\Models\ControlAlimento;
use Illuminate\Http\Request;

class ControlAlimentoController extends Controller
{
     public function postApiAddControlAlimento(Request $request) {
    // Obtenemos los parámetros de la petición
    $data = $request->all();
    $registro = new ControlAlimento();

    $registro->paciente_id = $data['paciente_id'];
    $registro->fecha_hora_recepcion = $data['fecha_hora_recepcion'];
    $registro->estado_alimentos = $data['estado_alimentos'];
    //$registro->cantidad_recibida = $data['cantidad_recibida'];
    $registro->detalle_alimentos = $data['detalle_alimentos'];
    $registro->alimentos_desechados = $data['alimentos_desechados'];
    $registro->motivo_desecho = $data['motivo_desecho'];
    $registro->paciente_consumio = $data['paciente_consumio'];
    $registro->observaciones_nutricionales = $data['observaciones_nutricionales'];
    $registro->entrega_alimentos_paciente = $data['entrega_alimentos_paciente'];

    $registro->save();

    // Opcional: Retornamos una respuesta JSON para que Axios sepa que todo salió bien
    return response()->json([
        'mensaje' => 'Registro guardado con éxito',
        'registro' => $registro
    ], 200);
}
}
