<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DashboardFarmaciaController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->input('mes', date('Y-m'));

        // RANGO DE FECHAS
        $inicio = Carbon::parse($mes . '-01')->startOfMonth();
        $fin = Carbon::parse($mes . '-01')->endOfMonth();

        // ================================
        // MOVIMIENTOS
        // ================================
        $movimientos = DB::table('movimientos_inventario as m')
            ->join('inventario as i', 'm.inventario_id', '=', 'i.id')
            ->join('medicamentos as med', 'i.medicamento_id', '=', 'med.id')
            ->leftJoin('distribuidores as d', 'm.proveedor_id', '=', 'd.id')
            ->select(
                'm.id',
                DB::raw('DATE(m.fecha_movimiento) as fecha'),
                'med.nombre as medicamento',
                'd.nombre as proveedor',
                'm.tipo',
                'm.cantidad'
            )
            ->whereBetween('m.fecha_movimiento', [$inicio, $fin])
            ->orderBy('m.fecha_movimiento', 'desc')
            ->get();

        // ================================
        // ENTRADAS / SALIDAS
        // ================================
        $entradasMes = $movimientos->where('tipo', 'entrada')->sum('cantidad');
        $salidasMes = $movimientos->where('tipo', 'salida')->sum('cantidad');

        // ================================
        // INVENTARIO
        // ================================
        $inventario = DB::table('inventario as i')
            ->join('medicamentos as m', 'i.medicamento_id', '=', 'm.id')
            ->select(
                'm.nombre',
                'i.stock',
                'i.stock_minimo as minimo',
                // PostgreSQL: resta de fechas en vez de DATEDIFF(a, b) + CURDATE()
                DB::raw('(i.fecha_caducidad - CURRENT_DATE) as "caducaEn"')
            )
            ->get();

        $productosBajos = $inventario->where('stock', '<', 'minimo')->values();
        $proximosCaducar = $inventario->where('caducaEn', '<=', 15)->values();

        // ================================
        // RECETAS
        // ================================
        $recetas = DB::table('recetas as r')
            ->join('pacientes as p', 'r.paciente_id', '=', 'p.id')
            ->join('usuarios as u', 'p.usuario_id', '=', 'u.id')
            ->leftJoin('receta_detalle as rd', 'r.id', '=', 'rd.receta_id')
            ->leftJoin('medicamentos as m', 'rd.medicamento_id', '=', 'm.id')
            ->select(
                'r.id',
                'u.nombre as paciente',
                // PostgreSQL: STRING_AGG en vez de GROUP_CONCAT
                DB::raw("STRING_AGG(m.nombre, ', ') as medicamento"),
                // PostgreSQL: TO_CHAR en vez de TIME()
                DB::raw("TO_CHAR(r.creado_en, 'HH24:MI:SS') as hora"),
                'r.estado as prioridad'
            )
            ->whereBetween('r.creado_en', [$inicio, $fin])
            ->groupBy('r.id', 'u.nombre', 'r.creado_en', 'r.estado')
            ->orderBy('r.creado_en', 'desc')
            ->get();

        // ================================
        // CONSUMO (TOP MEDICAMENTOS)
        // ================================
        $consumo = DB::table('movimientos_inventario as m')
            ->join('inventario as i', 'm.inventario_id', '=', 'i.id')
            ->join('medicamentos as med', 'i.medicamento_id', '=', 'med.id')
            ->select('med.nombre', DB::raw('SUM(m.cantidad) as total'))
            ->where('m.tipo', 'salida')
            ->whereBetween('m.fecha_movimiento', [$inicio, $fin])
            ->groupBy('med.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        // ================================
        // RECETAS (CON PAGINACIÓN)
        // ================================
        $perPage = $request->input('per_page', 5);

        $recetas = DB::table('recetas as r')
            ->join('pacientes as p', 'r.paciente_id', '=', 'p.id')
            ->join('usuarios as u', 'p.usuario_id', '=', 'u.id')

            //  DOCTOR
            ->leftJoin('doctores as doc', 'r.doctor_id', '=', 'doc.id')
            ->leftJoin('usuarios as u_doc', 'doc.usuario_id', '=', 'u_doc.id')

            ->leftJoin('receta_detalle as rd', 'r.id', '=', 'rd.receta_id')
            ->leftJoin('medicamentos as m', 'rd.medicamento_id', '=', 'm.id')

            ->select(
                'r.id',
                'r.doctor_id',

                'u.nombre as paciente',

                DB::raw("STRING_AGG(m.nombre, ', ') as medicamento"),
                DB::raw("TO_CHAR(r.creado_en, 'HH24:MI:SS') as hora"),
                'r.estado as prioridad',

                //  DOCTOR
                'u_doc.nombre as doctor',
                'u_doc.foto_url as foto_doctor',
                //  PACIENTE FOTO
                'u.foto_url as foto_paciente'
            )
            ->whereBetween('r.creado_en', [$inicio, $fin])
            ->groupBy(
                'r.id',
                'r.doctor_id',
                'u.nombre',
                'u.foto_url', // paciente
                'u_doc.nombre', // doctor nombre
                'u_doc.foto_url',
                'r.creado_en',
                'r.estado'
            )
            ->orderBy('r.creado_en', 'desc')
            ->paginate($perPage);

        // ================================
        // RESPUESTA
        // ================================
        return response()->json([
            'movimientos' => $movimientos,
            'entradasMes' => $entradasMes,
            'salidasMes' => $salidasMes,
            'inventario' => $inventario,
            'productosBajos' => $productosBajos,
            'proximosCaducar' => $proximosCaducar,
            'recetas' => $recetas,
            'recetasPendientes' => $recetas->count(),
            'consumo' => $consumo
        ]);
    }

    public function mostrar_imagen_usuario($nombre_imagen)
    {
        $path = storage_path('app/public/fotos/usuarios/' . $nombre_imagen);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        return Response::make($file, 200)->header("Content-Type", $type);
    }

    public function recetasHoy(Request $request)
    {
        $perPage = $request->input('per_page', 5);

        $hoyInicio = Carbon::today()->startOfDay();
        $hoyFin = Carbon::today()->endOfDay();

        $recetas = DB::table('recetas as r')
            ->join('pacientes as p', 'r.paciente_id', '=', 'p.id')
            ->join('usuarios as u', 'p.usuario_id', '=', 'u.id')

            ->leftJoin('doctores as doc', 'r.doctor_id', '=', 'doc.id')
            ->leftJoin('usuarios as u_doc', 'doc.usuario_id', '=', 'u_doc.id')

            ->leftJoin('receta_detalle as rd', 'r.id', '=', 'rd.receta_id')
            ->leftJoin('medicamentos as m', 'rd.medicamento_id', '=', 'm.id')
            ->leftJoin('inventario as i', 'm.id', '=', 'i.medicamento_id')

            ->select(
                'r.id',
                'r.estado',
                'u.nombre as paciente',
                'u.foto_url as foto_paciente',
                'u_doc.nombre as doctor',
                DB::raw("TO_CHAR(r.creado_en, 'HH24:MI:SS') as hora"),

                // PostgreSQL: STRING_AGG + CONCAT + COALESCE en vez de GROUP_CONCAT + IFNULL
                DB::raw("STRING_AGG(
                    CONCAT(
                        m.nombre, '|',
                        m.presentacion, '|',
                        m.requiere_receta, '|',
                        COALESCE(i.stock, 0), '|',
                        COALESCE(i.precio_venta, 0)
                    ), ';;'
                ) as medicamentos")
            )
            ->groupBy('r.id', 'r.estado', 'u.nombre', 'u.foto_url', 'u_doc.nombre', 'r.creado_en')
            ->whereBetween('r.creado_en', [$hoyInicio, $hoyFin])
            ->orderBy('r.creado_en', 'desc')
            ->paginate($perPage);

        return response()->json([
            'recetas' => $recetas
        ]);
    }
}