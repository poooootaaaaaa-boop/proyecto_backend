<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vacaciones_empleados')) {
            return;
        }

        Schema::create('vacaciones_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->unsignedTinyInteger('periodo')->default(1);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedSmallInteger('dias');
            $table->text('comentarios')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'cancelado'])->default('pendiente');
            $table->foreignId('resuelto_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('resuelto_en')->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            $table->index(['empleado_id', 'periodo', 'estado'], 'vacaciones_empleados_empleado_periodo_index');
            $table->index(['fecha_inicio', 'fecha_fin'], 'vacaciones_empleados_fechas_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacaciones_empleados');
    }
};