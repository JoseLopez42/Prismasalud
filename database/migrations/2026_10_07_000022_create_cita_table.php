<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "cita": citas médicas asignadas a un paciente (usuario),
     * con su médico, consultorio, tipo, estado, fecha y hora.
     */
    public function up(): void
    {
        Schema::create('cita', function (Blueprint $table) {
            $table->id();

            // Paciente: FK hacia la PK (cédula) de usuarios
            $table->unsignedInteger('cedula_usuario');

            // Médico: FK hacia la PK (cédula) de medico
            $table->unsignedInteger('cedula_medico');

            // Consultorio donde se atiende la cita
            $table->unsignedBigInteger('id_consultorio');

            // Catálogos de tipo y estado de la cita
            $table->unsignedBigInteger('id_tipo_cita');
            $table->unsignedBigInteger('id_estado_cita');

            $table->date('fecha');           // fecha exacta de la cita
            $table->time('hora_inicio');
            $table->time('hora_fin');

            // Motivo o razón de la cita (opcional)
            $table->string('motivo', 255)->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('cedula_usuario')
                  ->references('cedula')->on('usuarios')
                  ->onDelete('cascade')   // si se borra el paciente, se borran sus citas
                  ->onUpdate('cascade');

            $table->foreign('cedula_medico')
                  ->references('cedula')->on('medico')
                  ->onDelete('cascade')   // si se borra el médico, se borran sus citas
                  ->onUpdate('cascade');

            $table->foreign('id_consultorio')
                  ->references('id')->on('consultorio')
                  ->onDelete('restrict')  // no deja borrar un consultorio con citas asignadas
                  ->onUpdate('cascade');

            $table->foreign('id_tipo_cita')
                  ->references('id')->on('tipo_cita')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('id_estado_cita')
                  ->references('id')->on('estado_cita')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Índice para consultas por fecha (listados y reportes)
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};