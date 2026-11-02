<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabla ya existe en bases creadas a mano: no la vuelve a crear.
        if (Schema::hasTable('puestos')) {
            return;
        }

        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinica_id')->constrained('clinicas')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('salario_base', 12, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['clinica_id', 'nombre'], 'puestos_clinica_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puestos');
    }
};