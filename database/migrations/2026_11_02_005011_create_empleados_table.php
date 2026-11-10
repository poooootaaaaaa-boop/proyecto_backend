<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('empleados')) {
            return;
        }

        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinica_id')->constrained('clinicas')->cascadeOnDelete();
            $table->foreignId('puesto_id')->nullable()->constrained('puestos')->nullOnDelete();
            $table->string('numero_empleado', 20);
            $table->string('nombre', 100);
            $table->string('apellidos', 120);
            $table->string('correo', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('nss', 11)->nullable();
            $table->text('direccion')->nullable();
            $table->string('contacto_emergencia_nombre', 120)->nullable();
            $table->string('contacto_emergencia_telefono', 20)->nullable();
            $table->date('fecha_ingreso');
            $table->enum('tipo_contrato', ['indeterminado', 'determinado', 'honorarios', 'practicas'])->default('indeterminado');
            $table->decimal('salario_mensual', 12, 2)->nullable();
            $table->enum('estado', ['activo', 'baja'])->default('activo');
            $table->date('fecha_baja')->nullable();
            $table->string('motivo_baja', 255)->nullable();
            $table->timestamps();

            $table->unique(['clinica_id', 'numero_empleado'], 'empleados_clinica_numero_unique');
            $table->unique(['clinica_id', 'curp'], 'empleados_clinica_curp_unique');
            $table->index(['clinica_id', 'estado'], 'empleados_clinica_estado_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};