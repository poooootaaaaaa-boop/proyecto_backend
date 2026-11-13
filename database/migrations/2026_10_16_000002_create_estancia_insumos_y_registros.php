<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insumos_estancia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 180);
            $table->enum('categoria', ['medicamento', 'suero', 'material_medico']);
            $table->string('unidad_medida', 60);
            $table->decimal('precio_unitario', 12, 2)->unsigned();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['categoria', 'activo']);
        });

        Schema::create('estancia_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ocupacion_id')->constrained('ocupacion_habitaciones')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctores')->cascadeOnDelete();
            $table->enum('tipo', ['evolucion', 'procedimiento', 'observacion']);
            $table->text('descripcion');
            $table->decimal('costo', 12, 2)->unsigned()->nullable();
            $table->dateTime('fecha_hora');
            $table->timestamps();
            $table->index(['ocupacion_id', 'tipo', 'fecha_hora']);
        });

        Schema::create('estancia_consumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ocupacion_id')->constrained('ocupacion_habitaciones')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctores')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos_estancia')->restrictOnDelete();
            $table->decimal('cantidad', 12, 3)->unsigned();
            $table->decimal('precio_unitario', 12, 2)->unsigned();
            $table->decimal('subtotal', 14, 2)->unsigned();
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_hora');
            $table->timestamps();
            $table->index(['ocupacion_id', 'fecha_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estancia_consumos');
        Schema::dropIfExists('estancia_registros');
        Schema::dropIfExists('insumos_estancia');
    }
};