<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('consentimientos') && !Schema::hasColumn('consentimientos', 'fecha_finalizado')) {
            Schema::table('consentimientos', function (Blueprint $table) {
                $table->timestamp('fecha_finalizado')->nullable();
            });
        }

        if (Schema::hasTable('consentimientos')) {
            Schema::table('consentimientos', function (Blueprint $table) {
                $table->string('estado', 30)->default('Pendiente')->change();
            });
        }

        Schema::table('ocupacion_habitaciones', function (Blueprint $table) {
            foreach ([
                'motivo' => fn () => $table->string('motivo')->nullable(),
                'diagnostico' => fn () => $table->string('diagnostico')->nullable(),
                'dias_estimados' => fn () => $table->unsignedSmallInteger('dias_estimados')->nullable(),
                'fecha_alta_medica' => fn () => $table->dateTime('fecha_alta_medica')->nullable(),
                'doctor_alta_id' => fn () => $table->foreignId('doctor_alta_id')->nullable()->constrained('doctores')->nullOnDelete(),
                'diagnostico_egreso' => fn () => $table->string('diagnostico_egreso')->nullable(),
                'tratamiento_egreso' => fn () => $table->text('tratamiento_egreso')->nullable(),
                'medicamentos_egreso' => fn () => $table->text('medicamentos_egreso')->nullable(),
                'recomendaciones' => fn () => $table->text('recomendaciones')->nullable(),
                'proxima_consulta' => fn () => $table->date('proxima_consulta')->nullable(),
                'condicion_egreso' => fn () => $table->string('condicion_egreso')->nullable(),
                'notas_alta' => fn () => $table->text('notas_alta')->nullable(),
                'receta_id' => fn () => $table->foreignId('receta_id')->nullable()->constrained('recetas')->nullOnDelete(),
            ] as $column => $addColumn) {
                if (!Schema::hasColumn('ocupacion_habitaciones', $column)) {
                    $addColumn();
                }
            }
        });

        if (!Schema::hasTable('traslados_habitacion')) {
            Schema::create('traslados_habitacion', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ocupacion_id')->constrained('ocupacion_habitaciones')->cascadeOnDelete();
                $table->foreignId('habitacion_origen_id')->nullable()->constrained('habitaciones')->nullOnDelete();
                $table->foreignId('habitacion_destino_id')->constrained('habitaciones')->cascadeOnDelete();
                $table->foreignId('doctor_id')->nullable()->constrained('doctores')->nullOnDelete();
                $table->text('motivo')->nullable();
                $table->dateTime('fecha_traslado');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('consentimiento_hospitalizacion')) {
            Schema::create('consentimiento_hospitalizacion', function (Blueprint $table) {
                $table->id();
                $table->foreignId('consentimiento_id')->constrained('consentimientos')->cascadeOnDelete();
                $table->string('tipo', 30);
                $table->string('prioridad', 20)->default('Programada');
                $table->dateTime('fecha_ingreso')->nullable();
                $table->unsignedSmallInteger('dias_estimados')->nullable();
                $table->foreignId('habitacion_id')->nullable()->constrained('habitaciones')->nullOnDelete();
                $table->foreignId('ocupacion_id')->nullable()->constrained('ocupacion_habitaciones')->nullOnDelete();
                $table->string('diagnostico')->nullable();
                $table->text('motivo')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('consentimiento_vigilancia')) {
            Schema::create('consentimiento_vigilancia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('consentimiento_id')->constrained('consentimientos')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctores')->cascadeOnDelete();
                $table->timestamp('created_at')->nullable();
                $table->unique(['consentimiento_id', 'doctor_id']);
            });
        }

        if (!Schema::hasTable('consentimiento_firmas')) {
            Schema::create('consentimiento_firmas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('consentimiento_id')->constrained('consentimientos')->cascadeOnDelete();
                $table->string('tipo', 20);
                $table->longText('firma')->charset('binary');
                $table->string('mime', 50)->default('image/png');
                $table->dateTime('firmado_en')->nullable();
                $table->timestamps();
                $table->unique(['consentimiento_id', 'tipo']);
            });
        }

        if (!Schema::hasTable('firmas_registradas')) {
            Schema::create('firmas_registradas', function (Blueprint $table) {
                $table->id();
                $table->string('tipo', 20);
                $table->unsignedBigInteger('referencia_id');
                $table->longText('firma')->charset('binary');
                $table->string('mime', 50)->default('image/png');
                $table->timestamps();
                $table->unique(['tipo', 'referencia_id']);
            });
        }

        if (!Schema::hasTable('consentimiento_pdfs')) {
            Schema::create('consentimiento_pdfs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('consentimiento_id')->constrained('consentimientos')->cascadeOnDelete();
                $table->longText('contenido')->charset('binary');
                $table->string('mime', 50)->default('application/pdf');
                $table->unsignedBigInteger('tamano')->nullable();
                $table->dateTime('generado_en')->nullable();
                $table->timestamps();
                $table->unique('consentimiento_id');
            });
        }

        if (!Schema::hasTable('traslado_firmas')) {
            Schema::create('traslado_firmas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('traslado_id')->constrained('traslados_habitacion')->cascadeOnDelete();
                $table->string('tipo', 20);
                $table->string('nombre')->nullable();
                $table->longText('firma')->charset('binary');
                $table->string('mime', 50)->default('image/png');
                $table->timestamps();
                $table->unique(['traslado_id', 'tipo']);
            });
        }

        if (!Schema::hasTable('hospitalizacion_documentos')) {
            Schema::create('hospitalizacion_documentos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ocupacion_id')->nullable()->constrained('ocupacion_habitaciones')->nullOnDelete();
                $table->foreignId('habitacion_id')->nullable()->constrained('habitaciones')->nullOnDelete();
                $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
                $table->foreignId('doctor_id')->nullable()->constrained('doctores')->nullOnDelete();
                $table->string('tipo', 30);
                $table->string('nombre', 200);
                $table->longText('contenido')->charset('binary');
                $table->string('mime', 50)->default('application/pdf');
                $table->timestamps();
                $table->index(['tipo', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizacion_documentos');
        Schema::dropIfExists('traslado_firmas');
        Schema::dropIfExists('traslados_habitacion');
        Schema::dropIfExists('consentimiento_pdfs');
        Schema::dropIfExists('firmas_registradas');
        Schema::dropIfExists('consentimiento_firmas');
        Schema::dropIfExists('consentimiento_vigilancia');
        Schema::dropIfExists('consentimiento_hospitalizacion');

        Schema::table('ocupacion_habitaciones', function (Blueprint $table) {
            foreach ([
                'receta_id',
                'notas_alta',
                'condicion_egreso',
                'proxima_consulta',
                'recomendaciones',
                'medicamentos_egreso',
                'tratamiento_egreso',
                'diagnostico_egreso',
                'doctor_alta_id',
                'fecha_alta_medica',
                'dias_estimados',
                'diagnostico',
                'motivo',
            ] as $column) {
                if (Schema::hasColumn('ocupacion_habitaciones', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};