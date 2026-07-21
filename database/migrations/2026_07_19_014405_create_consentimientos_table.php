<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentimientos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->constrained('doctores')
                ->cascadeOnDelete();

            $table->foreignId('formato_id')
                ->constrained('formatos_consentimiento')
                ->cascadeOnDelete();

            $table->foreignId('consulta_id')
                ->nullable()
                ->constrained('consultas')
                ->nullOnDelete();

            $table->string('titulo')->nullable();

            $table->longText('contenido')->nullable();

            $table->string('firma')->nullable();

            $table->string('pdf')->nullable();

            $table->enum('estado', [
                'Pendiente',
                'Firmado',
                'Cancelado'
            ])->default('Pendiente');

            $table->timestamp('fecha_firma')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos');
    }
};