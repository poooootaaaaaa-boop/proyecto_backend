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
        Schema::create('reporte__habitacions', function (Blueprint $table) {
            $table->id();
            // 1. Apunta a la tabla 'habitaciones'
            $table->unsignedBigInteger('cuarto_id');
            $table->foreign('cuarto_id')
                ->references('id')
                ->on('habitaciones')
                ->onDelete('cascade');

            // 2. Apunta a la tabla 'instrumentos_medicos'
            $table->unsignedBigInteger('instrumento_id');
            $table->foreign('instrumento_id')
                ->references('id')
                ->on('instrumentos_medicos')
                ->onDelete('cascade');
            
            $table->text('descripcion');
            $table->string('foto')->nullable(); // Guarda la ruta/nombre de la imagen
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte__habitacions');
    }
};
