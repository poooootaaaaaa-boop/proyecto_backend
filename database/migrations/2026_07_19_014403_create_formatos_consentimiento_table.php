<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formatos_consentimiento', function (Blueprint $table) {

            $table->id();

            $table->string('nombre');

            $table->text('descripcion')->nullable();

            $table->longText('contenido');

            $table->boolean('requiere_firma')->default(true);

            $table->boolean('activo')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formatos_consentimiento');
    }
};
