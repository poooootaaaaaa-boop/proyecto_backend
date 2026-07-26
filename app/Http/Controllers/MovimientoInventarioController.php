<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimientoInventario;
use App\Models\Inventario;
use App\Models\Medicamento;
use Illuminate\Support\Facades\DB; // Importamos DB para consultas rápidas de fallback

class MovimientoInventarioController extends Controller
{
    // =====================================
    // GUARDAR MOVIMIENTO (SOPORTA CARRITO)
    // =====================================
    public function store(Request $request)
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | VALIDACIÓN GENERAL
            |--------------------------------------------------------------------------
            */
            $request->validate([
                'tipo' => 'required|in:entrada,salida',
                'motivo' => 'nullable|string',
                'proveedor_id' => 'nullable',
                'receta_id' => 'nullable',
                'caducado' => 'nullable|boolean',
                'medicamentos' => 'required|array|min:1'
            ]);

            /*
            |--------------------------------------------------------------------------
            | VALIDACIÓN DE REGLAS DE NEGOCIO
            |--------------------------------------------------------------------------
            */
            if ($request->tipo == "entrada" && !$request->proveedor_id) {
                return response()->json([
                    "message" => "Proveedor obligatorio para entrada"
                ], 400);
            }

            // No se exige receta_id si es una salida por concepto de medicamento caducado
            if ($request->tipo == "salida" && !$request->receta_id && !$request->caducado) {
                return response()->json([
                    "message" => "Receta obligatoria para salida de stock activo"
                ], 400);
            }

            /*
            |--------------------------------------------------------------------------
            | OBTENER CLAVES FORÁNEAS DINÁMICAS (Para evitar fallos de integridad)
            |--------------------------------------------------------------------------
            */
            // Intentamos obtener el usuario autenticado, si no hay, tomamos el primero de la BD
            $usuario_id = auth()->id();
            if (!$usuario_id) {
                $primerUsuario = DB::table('usuarios')->first();
                $usuario_id = $primerUsuario ? $primerUsuario->id : null;
            }

            if (!$usuario_id) {
                return response()->json([
                    "message" => "No se encontró ningún usuario registrado en el sistema."
                ], 400);
            }

            // Intentamos obtener la primera farmacia registrada en la BD
            $primeraFarmacia = DB::table('farmacias')->first();
            $farmacia_id = $primeraFarmacia ? $primeraFarmacia->id : null;

            if (!$farmacia_id) {
                return response()->json([
                    "message" => "No se encontró ninguna farmacia registrada en el sistema."
                ], 400);
            }

            /*
            |--------------------------------------------------------------------------
            | PROCESAR ELEMENTOS DEL CARRITO
            |--------------------------------------------------------------------------
            */
            foreach ($request->medicamentos as $item) {
                $medicamento_id = $item['medicamento_id'];
                $cantidad = intval($item['cantidad']);

                // Buscar el registro de inventario de este medicamento
                $inventario = Inventario::where('medicamento_id', $medicamento_id)->first();

                /*
                =====================================
                PROCESAR ENTRADAS
                =====================================
                */
                if ($request->tipo == "entrada") {
                    if (!$inventario) {
                        // Creación con farmacia_id dinámico y válido
                        $inventario = Inventario::create([
                            'farmacia_id' => $farmacia_id,
                            'medicamento_id' => $medicamento_id,
                            'stock' => $cantidad,
                            'stock_minimo' => 5
                        ]);
                    } else {
                        $inventario->stock += $cantidad;
                        $inventario->save();
                    }
                }
                /*
                =====================================
                PROCESAR SALIDAS
                =====================================
                */
                else {
                    if (!$inventario) {
                        return response()->json([
                            "message" => "No existe inventario para el medicamento con ID " . $medicamento_id
                        ], 404);
                    }

                    if ($inventario->stock < $cantidad) {
                        return response()->json([
                            "message" => "Stock insuficiente para el medicamento con ID " . $medicamento_id
                        ], 400);
                    }

                    $inventario->stock -= $cantidad;
                    $inventario->save();
                }

                /*
                =====================================
                REGISTRAR EL MOVIMIENTO HISTÓRICO
                =====================================
                */
                MovimientoInventario::create([
                    'inventario_id' => $inventario->id,
                    'tipo' => $request->tipo,
                    'cantidad' => $cantidad,
                    'motivo' => $request->motivo,
                    'proveedor_id' => $request->tipo === "entrada" ? $request->proveedor_id : null,
                    'receta_id' => $request->tipo === "salida" ? $request->receta_id : null,
                    'usuario_id' => $usuario_id, // ID dinámico validado
                    'fecha_movimiento' => now()
                ]);
            }

            return response()->json([
                "message" => "Movimientos registrados correctamente"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error al guardar movimiento",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // =====================================
    // HISTORIAL
    // =====================================
    public function index()
    {
        $movimientos = MovimientoInventario::with([
            'inventario.medicamento',
            'proveedor',
            'receta'
        ])
        ->orderBy('fecha_movimiento', 'desc')
        ->get();

        return response()->json($movimientos);
    }

    // =====================================
    // MEDICAMENTOS SELECT
    // =====================================
    public function medicamentos()
    {
        $medicamentos = Medicamento::select('id', 'nombre')->get();
        return response()->json($medicamentos);
    }

    // =====================================
    // STOCK POR MEDICAMENTO
    // =====================================
    public function stock($medicamento_id)
    {
        $inventario = Inventario::where('medicamento_id', $medicamento_id)->first();

        if (!$inventario) {
            return response()->json([
                "message" => "No hay inventario"
            ], 404);
        }

        return response()->json([
            "stock" => $inventario->stock
        ]);
    }
}