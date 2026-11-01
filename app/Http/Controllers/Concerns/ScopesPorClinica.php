<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Utilidades compartidas por los controladores de Gestión de Personal.
 * Todo registro pertenece a una clínica: el frontend envía `clinica_id`
 * (usuario.clinica_id del login) y aquí se valida y se usa para aislar datos.
 */
trait ScopesPorClinica
{
    protected function clinicaId(Request $request): int
    {
        $data = $request->validate([
            'clinica_id' => 'required|integer|exists:clinicas,id',
        ], $this->mensajes(), $this->atributos());

        return (int) $data['clinica_id'];
    }

    /** ID del usuario que registra la acción (opcional, para auditoría). */
    protected function registradoPor(Request $request): ?int
    {
        $data = $request->validate([
            'registrado_por' => 'nullable|integer|exists:usuarios,id',
        ], $this->mensajes(), $this->atributos());

        return isset($data['registrado_por']) ? (int) $data['registrado_por'] : null;
    }

    /** Aborta con 404 si el recurso pertenece a otra clínica. */
    protected function asegurarClinica(Request $request, $clinicaDelRecurso): void
    {
        abort_unless(
            (int) $clinicaDelRecurso === $this->clinicaId($request),
            404,
            'El registro no pertenece a esta clínica.'
        );
    }

    protected function mensajes(): array
    {
        return [
            'required'            => 'El campo :attribute es obligatorio.',
            'required_if'         => 'El campo :attribute es obligatorio.',
            'email'               => 'El campo :attribute debe ser un correo válido.',
            'unique'              => 'Ya existe un registro con ese :attribute.',
            'exists'              => 'El :attribute seleccionado no es válido.',
            'date'                => 'El campo :attribute debe ser una fecha válida.',
            'date_format'         => 'El campo :attribute debe tener formato HH:MM.',
            'after_or_equal'      => 'El campo :attribute no puede ser anterior a la fecha de inicio.',
            'before'              => 'El campo :attribute debe ser una fecha pasada.',
            'in'                  => 'El valor de :attribute no es válido.',
            'integer'             => 'El campo :attribute debe ser un número entero.',
            'numeric'             => 'El campo :attribute debe ser numérico.',
            'min'                 => 'El campo :attribute es demasiado pequeño o corto.',
            'max'                 => 'El campo :attribute es demasiado grande o largo.',
            'size'                => 'El campo :attribute tiene una longitud incorrecta.',
            'boolean'             => 'El campo :attribute debe ser verdadero o falso.',
            'array'               => 'El campo :attribute no es válido.',
        ];
    }

    protected function atributos(): array
    {
        return [
            'clinica_id'                   => 'clínica',
            'puesto_id'                    => 'puesto',
            'empleado_id'                  => 'empleado',
            'nombre'                       => 'nombre',
            'apellidos'                    => 'apellidos',
            'correo'                       => 'correo',
            'telefono'                     => 'teléfono',
            'fecha_nacimiento'             => 'fecha de nacimiento',
            'curp'                         => 'CURP',
            'rfc'                          => 'RFC',
            'nss'                          => 'NSS',
            'fecha_ingreso'                => 'fecha de ingreso',
            'fecha_baja'                   => 'fecha de baja',
            'motivo_baja'                  => 'motivo de baja',
            'salario_mensual'              => 'salario mensual',
            'salario_base'                 => 'salario base',
            'tipo_contrato'                => 'tipo de contrato',
            'contacto_emergencia_nombre'   => 'contacto de emergencia',
            'contacto_emergencia_telefono' => 'teléfono de emergencia',
            'fecha_inicio'                 => 'fecha de inicio',
            'fecha_fin'                    => 'fecha de fin',
            'hora_inicio'                  => 'hora de inicio',
            'hora_fin'                     => 'hora de fin',
            'dia_semana'                   => 'día de la semana',
            'tipo'                         => 'tipo',
            'motivo'                       => 'motivo',
            'estado'                       => 'estado',
            'descripcion'                  => 'descripción',
            'fecha'                        => 'fecha',
        ];
    }
}