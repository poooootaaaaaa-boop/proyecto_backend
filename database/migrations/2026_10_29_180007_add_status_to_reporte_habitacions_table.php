
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporte__habitacions', function (Blueprint $table) {
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])
                ->default('pendiente');
        });
    }

    public function down(): void
    {
        Schema::table('reporte__habitacions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
