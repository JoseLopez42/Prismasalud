<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estado de dispensación de la prescripción: nace "pendiente" y el
     * farmaceuta la marca como "entregada" registrando fecha, cantidad,
     * quién la entregó y una observación opcional.
     */
    public function up(): void
    {
        Schema::table('prescripcion', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente')->after('activo');
            $table->dateTime('fecha_entrega')->nullable()->after('estado');
            $table->unsignedBigInteger('cedula_farmaceuta')->nullable()->after('fecha_entrega');
            $table->unsignedInteger('cantidad_entregada')->nullable()->after('cedula_farmaceuta');
            $table->string('observacion_entrega', 255)->nullable()->after('cantidad_entregada');

            $table->foreign('cedula_farmaceuta')
                  ->references('cedula')->on('usuarios')
                  ->nullOnDelete();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::table('prescripcion', function (Blueprint $table) {
            $table->dropForeign(['cedula_farmaceuta']);
            $table->dropIndex(['estado']);
            $table->dropColumn([
                'estado', 'fecha_entrega', 'cedula_farmaceuta',
                'cantidad_entregada', 'observacion_entrega',
            ]);
        });
    }
};
