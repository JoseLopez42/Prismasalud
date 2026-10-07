<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "horario_medico": fechas y horas en que un médico atiende
     * en un consultorio. Un médico puede tener varios horarios
     * y un consultorio puede ser usado por varios médicos.
     */
    public function up(): void
    {
        Schema::create('horario_medico', function (Blueprint $table) {
            $table->id();

            // FK hacia la PK de medico (cédula, INT sin autoincrementar)
            $table->unsignedInteger('cedula_medico');

            // FK hacia consultorio
            $table->unsignedBigInteger('id_consultorio');

            $table->date('fecha');              // fecha exacta de atención
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('cedula_medico')
                  ->references('cedula')->on('medico')
                  ->onDelete('cascade')   // si se borra el médico, se borran sus horarios
                  ->onUpdate('cascade');

            $table->foreign('id_consultorio')
                  ->references('id')->on('consultorio')
                  ->onDelete('restrict')  // no deja borrar un consultorio con horarios asignados
                  ->onUpdate('cascade');

            // Evita que el mismo médico quede con la misma hora de inicio
            // en la misma fecha (dos citas solapadas a la misma hora).
            $table->unique(['cedula_medico', 'fecha', 'hora_inicio'], 'horario_medico_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario_medico');
    }
};
