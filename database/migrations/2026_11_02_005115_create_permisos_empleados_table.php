<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permisos_empleados')) {
            return;
        }

        Schema::create('permisos_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->enum('tipo', ['personal', 'medico', 'estudio', 'duelo', 'maternidad_paternidad', 'otro'])->default('personal');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedSmallInteger('dias')->default(1);
            $table->boolean('con_goce_sueldo')->default(true);
            $table->text('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'cancelado'])->default('pendiente');
            $table->foreignId('resuelto_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('resuelto_en')->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            $table->index(['empleado_id', 'estado'], 'permisos_empleados_empleado_estado_index');
            $table->index(['fecha_inicio', 'fecha_fin'], 'permisos_empleados_fechas_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_empleados');
    }
};