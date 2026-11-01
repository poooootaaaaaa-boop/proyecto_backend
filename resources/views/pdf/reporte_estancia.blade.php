@php
    $money = fn ($value) => '$' . number_format((float) $value, 2);
    $fecha = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y H:i') : '—';
    $tiposRegistro = [
        'evolucion' => 'Evolución',
        'procedimiento' => 'Procedimiento',
        'observacion' => 'Observación',
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte final de estancia #{{ $estancia['id'] }}</title>
    <style>
        @page { margin: 38px 42px 48px; }
        body { color: #18323d; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.45; }
        h1 { margin: 0; color: #0f766e; font-size: 19px; }
        h2 { margin: 20px 0 7px; color: #0f766e; font-size: 12px; border-bottom: 1px solid #d4e1e3; padding-bottom: 4px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 10px; margin-bottom: 12px; }
        .subtitle { color: #657a82; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th { color: #45616a; background: #e9f2f1; text-align: left; font-size: 8px; }
        th, td { padding: 6px 7px; border-bottom: 1px solid #e6edef; vertical-align: top; }
        .meta td:first-child { width: 28%; color: #657a82; font-weight: bold; }
        .money { text-align: right; white-space: nowrap; }
        .totals { width: 58%; margin: 0 0 0 auto; }
        .grand { font-size: 11px; font-weight: bold; color: #0f766e; }
        .empty { color: #71858c; font-style: italic; padding: 8px 0; }
        .footer { position: fixed; bottom: -26px; width: 100%; text-align: center; color: #71858c; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte final de estancia</h1>
        <div class="subtitle">Folio #{{ $estancia['id'] }} · Generado {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <h2>Datos del paciente y estancia</h2>
    <table class="meta">
        <tr><td>Paciente</td><td>{{ $estancia['paciente']['usuario']['nombre'] ?? '—' }}</td></tr>
        <tr><td>Habitación</td><td>{{ isset($estancia['habitacion']['numero']) ? 'Hab. ' . $estancia['habitacion']['numero'] : '—' }}</td></tr>
        <tr><td>Ingreso</td><td>{{ $fecha($estancia['fecha_ingreso'] ?? null) }}</td></tr>
        <tr><td>Salida</td><td>{{ $fecha($estancia['fecha_salida'] ?? null) }}</td></tr>
        <tr><td>Diagnóstico de ingreso</td><td>{{ $estancia['diagnostico'] ?? '—' }}</td></tr>
        <tr><td>Diagnóstico de egreso</td><td>{{ $estancia['diagnostico_egreso'] ?? '—' }}</td></tr>
        <tr><td>Condición de egreso</td><td>{{ $estancia['condicion_egreso'] ?? '—' }}</td></tr>
    </table>

    <h2>Evolución, procedimientos y observaciones</h2>
    @if(count($estancia['registros']))
        <table>
            <thead><tr><th>Fecha y hora</th><th>Tipo</th><th>Doctor</th><th>Detalle</th><th class="money">Costo</th></tr></thead>
            <tbody>
                @foreach($estancia['registros'] as $registro)
                    <tr>
                        <td>{{ $fecha($registro['fecha_hora'] ?? null) }}</td>
                        <td>{{ $tiposRegistro[$registro['tipo']] ?? $registro['tipo'] }}</td>
                        <td>{{ $registro['doctor']['usuario']['nombre'] ?? '—' }}</td>
                        <td>{{ $registro['descripcion'] }}</td>
                        <td class="money">{{ $registro['tipo'] === 'procedimiento' ? $money($registro['costo'] ?? 0) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Sin registros clínicos de estancia.</div>
    @endif

    <h2>Medicamentos, sueros y material médico</h2>
    @if(count($estancia['consumos']))
        <table>
            <thead><tr><th>Fecha y hora</th><th>Categoría</th><th>Insumo</th><th>Cantidad</th><th class="money">Precio unitario</th><th class="money">Subtotal</th></tr></thead>
            <tbody>
                @foreach($estancia['consumos'] as $consumo)
                    <tr>
                        <td>{{ $fecha($consumo['fecha_hora'] ?? null) }}</td>
                        <td>{{ $consumo['insumo']['categoria'] ?? '—' }}</td>
                        <td>{{ $consumo['insumo']['nombre'] ?? 'Insumo eliminado' }}@if(!empty($consumo['observaciones']))<br><span class="subtitle">{{ $consumo['observaciones'] }}</span>@endif</td>
                        <td>{{ number_format((float) $consumo['cantidad'], 3) }} {{ $consumo['insumo']['unidad_medida'] ?? '' }}</td>
                        <td class="money">{{ $money($consumo['precio_unitario']) }}</td>
                        <td class="money">{{ $money($consumo['subtotal']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h2>Desglose de costos</h2>
        <table class="totals">
            @foreach($estancia['costos_por_categoria'] as $costo)
                <tr><td>{{ str_replace('_', ' ', ucfirst($costo['categoria'])) }}</td><td class="money">{{ $money($costo['total']) }}</td></tr>
            @endforeach
            <tr class="grand"><td>Total de estancia</td><td class="money">{{ $money($estancia['costo_total']) }}</td></tr>
        </table>
    @else
        <div class="empty">Sin consumos registrados durante la estancia.</div>
        <table class="totals"><tr class="grand"><td>Total de estancia</td><td class="money">{{ $money($estancia['costo_total']) }}</td></tr></table>
    @endif

    <div class="footer">CliniConnect · Reporte final de estancia #{{ $estancia['id'] }}</div>
</body>
</html>
