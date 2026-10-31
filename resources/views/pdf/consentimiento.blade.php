{{-- resources/views/pdf/consentimiento.blade.php --}}
@php
    $h = $c->hospitalizacion ?? null;
    $fecha = fn ($valor, $formato = 'd/m/Y') => $valor ? \Carbon\Carbon::parse($valor)->format($formato) : '—';
    $habitacion = ($h && $h->habitacion_numero)
        ? 'Hab. ' . $h->habitacion_numero . ($h->habitacion_piso ? ' · Piso ' . $h->habitacion_piso : '')
        : 'Sin asignar';
    $bloques = [
        ['clave' => 'clinica',  'titulo' => 'Clínica',            'nombre' => $c->clinica_nombre ?? ''],
        ['clave' => 'doctor',   'titulo' => 'Doctor responsable', 'nombre' => $c->doctor_nombre],
        ['clave' => 'paciente', 'titulo' => 'Paciente',           'nombre' => $c->paciente_nombre],
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Consentimiento #{{ $c->id }}</title>
<style>
    @page { margin: 40px 46px 56px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #0e2a3b; line-height: 1.5; }
    table { border-collapse: collapse; width: 100%; }
    td, th { vertical-align: top; }

    .cabecera td { padding-bottom: 10px; border-bottom: 2px solid #0f766e; }
    .clinica-nombre { font-size: 16px; font-weight: bold; }
    .muted { color: #5c7482; font-size: 9.5px; }
    .folio { text-align: right; font-size: 9.5px; color: #5c7482; }
    .folio strong { display: block; font-size: 13px; color: #0e2a3b; }

    h1 { font-size: 15px; margin: 18px 0 10px; }
    h2 { font-size: 10.5px; margin: 16px 0 6px; padding-bottom: 3px; border-bottom: 1px solid #d9e3e6; color: #0b5a54; }

    .datos td { padding: 3px 0; }
    .datos .k { width: 27%; color: #5c7482; }
    .prioridad { font-weight: bold; }

    .contenido { text-align: justify; margin-top: 4px; }

    .firmas { margin-top: 34px; page-break-inside: avoid; }
    .firmas td { width: 33.33%; text-align: center; padding: 0 10px; }
    .firma-espacio { height: 70px; }
    .firma-espacio img { max-height: 64px; max-width: 170px; }
    .firma-linea { border-top: 1px solid #0e2a3b; padding-top: 4px; }
    .firma-rol { font-weight: bold; font-size: 10px; }
    .firma-nombre { font-size: 9.5px; color: #46606f; }

    .pie { position: fixed; bottom: -34px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #7d929e; }
</style>
</head>
<body>

    <table class="cabecera">
        <tr>
            <td>
                <div class="clinica-nombre">{{ $clinica->nombre ?? $c->clinica_nombre ?? 'Clínica' }}</div>
                @if($clinica)
                    <div class="muted">
                        {{ collect([$clinica->direccion, $clinica->ciudad, $clinica->estado])->filter()->implode(', ') }}
                        @if($clinica->telefono) · Tel. {{ $clinica->telefono }} @endif
                    </div>
                @endif
            </td>
            <td class="folio">
                Consentimiento informado
                <strong>No. {{ str_pad($c->id, 6, '0', STR_PAD_LEFT) }}</strong>
                {{ $fecha($c->created_at) }}
            </td>
        </tr>
    </table>

    <h1>{{ $c->titulo }}</h1>

    <table class="datos">
        <tr><td class="k">Paciente</td><td>{{ $c->paciente_nombre }}</td></tr>
        <tr><td class="k">Doctor responsable</td><td>{{ $c->doctor_nombre }}</td></tr>
        @if($c->observaciones)
            <tr><td class="k">Observaciones</td><td>{!! nl2br(e($c->observaciones)) !!}</td></tr>
        @endif
    </table>

    @if($h)
        <h2>Datos de la hospitalización</h2>
        <table class="datos">
            <tr><td class="k">Tipo</td><td>{{ $tiposHosp[$h->tipo] ?? $h->tipo }}</td></tr>
            <tr><td class="k">Prioridad</td><td class="prioridad">{{ $h->prioridad }}</td></tr>
            <tr><td class="k">Fecha de ingreso</td><td>{{ $fecha($h->fecha_ingreso) }}</td></tr>
            <tr><td class="k">Estancia estimada</td><td>{{ $h->dias_estimados ? $h->dias_estimados . ' día(s)' : '—' }}</td></tr>
            <tr><td class="k">Habitación</td><td>{{ $habitacion }}</td></tr>
            <tr><td class="k">Diagnóstico</td><td>{{ $h->diagnostico ?: '—' }}</td></tr>
            <tr><td class="k">Motivo</td><td>{!! $h->motivo ? nl2br(e($h->motivo)) : '—' !!}</td></tr>
            <tr>
                <td class="k">Doctores de vigilancia</td>
                <td>
                    @forelse($c->vigilancia as $v)
                        {{ $v->nombre }}@if(!$loop->last), @endif
                    @empty
                        —
                    @endforelse
                </td>
            </tr>
        </table>
    @endif

    <h2>Consentimiento</h2>
    <div class="contenido">{!! nl2br(e($c->contenido)) !!}</div>

    <table class="firmas">
        <tr>
            @foreach($bloques as $b)
                <td>
                    <div class="firma-espacio">
                        @if(!empty($firmas[$b['clave']]))
                            <img src="{{ $firmas[$b['clave']] }}" alt="Firma {{ $b['titulo'] }}">
                        @endif
                    </div>
                    <div class="firma-linea">
                        <div class="firma-rol">{{ $b['titulo'] }}</div>
                        <div class="firma-nombre">{{ $b['nombre'] }}</div>
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    <div class="pie">
        Documento No. {{ str_pad($c->id, 6, '0', STR_PAD_LEFT) }} · Generado el {{ now()->format('d/m/Y H:i') }}
        @if($c->fecha_firma) · Firmado por el paciente el {{ $fecha($c->fecha_firma, 'd/m/Y H:i') }} @endif
    </div>

</body>
</html>