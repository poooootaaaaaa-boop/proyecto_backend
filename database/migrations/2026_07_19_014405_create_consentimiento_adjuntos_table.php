<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentimiento_adjuntos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('consentimiento_id')
                ->constrained('consentimientos')
                ->cascadeOnDelete();

            $table->string('archivo');

            $table->string('tipo')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimiento_adjuntos');
    }
};