<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enlaza cada receta con el catálogo de medicamentos. La prescripción
     * pasa a referenciar el "codigo" del medicamento (llave primaria del
     * catálogo), de modo que más adelante el farmaceuta pueda saber
     * exactamente qué medicamento debe dispensar.
     *
     * El campo "medicamento" (nombre en texto) se conserva por
     * compatibilidad y se mantiene sincronizado desde el controlador.
     */
    public function up(): void
    {
        if (Schema::hasColumn('prescripcion', 'id_medicamento')) {
            return;
        }

        Schema::table('prescripcion', function (Blueprint $table) {
            $table->string('id_medicamento', 50)->nullable()->after('cedula_medico');

            $table->foreign('id_medicamento')
                  ->references('codigo')->on('medicamento')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('prescripcion', function (Blueprint $table) {
            $table->dropForeign(['id_medicamento']);
            $table->dropColumn('id_medicamento');
        });
    }
};
