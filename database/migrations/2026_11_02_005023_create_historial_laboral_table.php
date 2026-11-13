<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('historial_laboral')) {
            return;
        }

        Schema::create('historial_laboral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->enum('tipo', [
                'alta', 'cambio_puesto', 'cambio_salario', 'baja', 'reingreso',
                'permiso', 'vacaciones', 'reconocimiento', 'amonestacion',
                'capacitacion', 'otro',
            ]);
            $table->date('fecha');
            $table->string('descripcion', 500);
            $table->string('valor_anterior', 255)->nullable();
            $table->string('valor_nuevo', 255)->nullable();
            $table->boolean('automatico')->default(false);
            $table->foreignId('registrado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['empleado_id', 'fecha'], 'historial_laboral_empleado_fecha_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_laboral');
    }
};