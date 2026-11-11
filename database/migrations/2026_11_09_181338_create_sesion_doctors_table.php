<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up(): void
{
    Schema::create('sesiones_doctores', function (Blueprint $table) {
        $table->id();

        $table->foreignId('usuario_id')
            ->constrained('usuarios')
            ->cascadeOnDelete();

        $table->unsignedBigInteger('token_id')->nullable()->index();

        $table->dateTime('inicio_sesion');
        $table->dateTime('fin_sesion')->nullable();

        $table->string('estado', 20)->default('activa');

        $table->timestamps();

        $table->index(['usuario_id', 'inicio_sesion']);
    });
}

public function down(): void
{
    Schema::dropIfExists('sesiones_doctores');
}

};
