<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('control_alimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes');
            $table->dateTime('fecha_hora_recepcion');
            $table->enum('estado_alimentos', ['bueno', 'regular', 'malo']);
            //$table->decimal('cantidad_recibida', 10, 2);
            // NUEVO
            $table->json('detalle_alimentos')->nullable();
            $table->text('alimentos_desechados')->nullable();
            $table->text('motivo_desecho')->nullable();
            $table->boolean('paciente_consumio');
            $table->text('observaciones_nutricionales')->nullable();
            $table->boolean('entrega_alimentos_paciente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_alimentos');
    }
};
