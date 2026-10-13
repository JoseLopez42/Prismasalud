<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "historia_clinica": registro que crea el médico al
     * atender a un paciente (normalmente a partir de una cita
     * programada). Es la cabecera de la que cuelgan los
     * diagnósticos y las enfermedades del paciente.
     */
    public function up(): void
    {
        Schema::create('historia_clinica', function (Blueprint $table) {
            $table->id();

            // Paciente (usuario) al que corresponde la historia
            $table->unsignedInteger('cedula_paciente');

            // Médico que elabora la historia
            $table->unsignedInteger('cedula_medico');

            // Cita programada que originó la atención (opcional)
            $table->unsignedBigInteger('id_cita')->nullable();

            $table->date('fecha');

            $table->text('motivo_consulta')->nullable();
            $table->text('antecedentes')->nullable();
            $table->text('examen_fisico')->nullable();
            $table->text('observaciones')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('cedula_paciente')
                  ->references('cedula')->on('usuarios')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('cedula_medico')
                  ->references('cedula')->on('medico')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_cita')
                  ->references('id')->on('cita')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historia_clinica');
    }
};
