@php
    $fecha = fn ($valor) => $valor ? \Carbon\Carbon::parse($valor)->format('d/m/Y H:i') : '—';
    $roles = [
        'Doctor' => $doctor->usuario->nombre ?? 'Doctor responsable',
        'Familiar' => $datos['Nombre del familiar'] ?? 'Familiar',
        'Paciente' => $paciente->usuario->nombre ?? 'Paciente',
        'Clinica' => $clinica->nombre ?? 'Clínica',
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $nombre }}</title>
    <style>
        @page { margin: 42px 48px 54px; }
        body { color: #17313d; font-family: DejaVu Sans, sans-serif; font-size: 10.5px; line-height: 1.5; }
        h1 { color: #0f766e; font-size: 19px; margin: 24px 0 6px; }
        h2 { border-bottom: 1px solid #d8e2e5; color: #0f766e; font-size: 12px; margin: 22px 0 8px; padding-bottom: 4px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 12px; }
        .clinic { font-size: 15px; font-weight: bold; }
        .muted { color: #607783; }
        .folio { float: right; text-align: right; }
        table { border-collapse: collapse; width: 100%; }
        .datos td { border-bottom: 1px solid #edf1f2; padding: 7px 4px; vertical-align: top; }
        .datos td:first-child { color: #607783; font-weight: bold; width: 30%; }
        .firmas { margin-top: 30px; page-break-inside: avoid; }
        .firmas td { padding: 10px 8px; text-align: center; vertical-align: bottom; width: 33.33%; }
        .firma-imagen { height: 62px; }
        .firma-imagen img { max-height: 58px; max-width: 150px; }
        .linea { border-top: 1px solid #17313d; padding-top: 5px; }
        .pie { bottom: -30px; color: #748991; font-size: 8px; position: fixed; text-align: center; width: 100%; }
    </style>
</head>
<body>
    <div class="header">
        <div class="folio">Documento<br><strong>{{ str_pad((string) ($documentoId ?? ''), 6, '0', STR_PAD_LEFT) }}</strong><br>{{ now()->format('d/m/Y H:i') }}</div>
        <div class="clinic">{{ $clinica->nombre ?? 'Clínica' }}</div>
        <div class="muted">Gestión hospitalaria</div>
    </div>

    <h1>{{ $nombre }}</h1>
    <table class="datos">
        <tr><td>Tipo</td><td>{{ $tipo }}</td></tr>
        <tr><td>Paciente</td><td>{{ $paciente->usuario->nombre ?? '—' }}</td></tr>
        <tr><td>Doctor responsable</td><td>{{ $doctor->usuario->nombre ?? '—' }}</td></tr>
        <tr><td>Habitación actual</td><td>{{ $habitacion ? 'Hab. ' . $habitacion->numero : '—' }}</td></tr>
        @foreach($datos as $etiqueta => $valor)
            @if($etiqueta !== 'Nombre del familiar')
                <tr><td>{{ $etiqueta }}</td><td>{!! nl2br(e(is_scalar($valor) ? (string) $valor : '—')) !!}</td></tr>
            @endif
        @endforeach
    </table>

    @if($firmas)
        <h2>Firmas de autorización del traslado</h2>
        <table class="firmas">
            @foreach(array_chunk($firmas, 3, true) as $grupo)
                <tr>
                    @foreach($grupo as $tipoFirma => $imagen)
                        <td>
                            <div class="firma-imagen"><img src="{{ $imagen }}" alt="Firma {{ $tipoFirma }}"></div>
                            <div class="linea"><strong>{{ $tipoFirma }}</strong><br>{{ $roles[$tipoFirma] ?? $tipoFirma }}</div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    @endif

    <div class="pie">Documento generado el {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>