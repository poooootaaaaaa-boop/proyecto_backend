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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('clinica_id')
                ->constrained('clinicas')
                ->onDelete('cascade');

            $table->string('numero', 20)->nullable();
            $table->string('piso', 20)->nullable();

            $table->enum('tipo', [
                'Individual',
                'Compartida',
                'Urgencias',
                'Quirófano',
                'UCI'
            ])->nullable();

            $table->enum('estado', [
                'Disponible',
                'Ocupada',
                'Mantenimiento',
                'Limpieza'
            ])->default('Disponible');

            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
