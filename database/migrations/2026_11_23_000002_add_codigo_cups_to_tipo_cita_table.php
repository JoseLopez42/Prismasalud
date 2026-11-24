<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega los campos necesarios para identificar cada tipo de cita
     * con su código oficial CUPS del Ministerio de Salud de Colombia.
     */
    public function up(): void
    {
        Schema::table('tipo_cita', function (Blueprint $table) {
            $table->string('codigo_cups', 10)->nullable()->unique()->after('nombre');
            $table->string('categoria', 80)->nullable()->after('descripcion');
            $table->string('fuente', 60)->nullable()->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('tipo_cita', function (Blueprint $table) {
            $table->dropUnique(['codigo_cups']);
            $table->dropColumn(['codigo_cups', 'categoria', 'fuente']);
        });
    }
};
