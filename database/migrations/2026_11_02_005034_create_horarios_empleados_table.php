<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('horarios_empleados')) {
            return;
        }

        Schema::create('horarios_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['empleado_id', 'dia_semana'], 'horarios_empleados_empleado_dia_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_empleados');
    }
};