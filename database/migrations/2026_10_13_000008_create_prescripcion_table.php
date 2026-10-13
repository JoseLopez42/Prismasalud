<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "prescripcion": medicamento que el médico le formula al
     * paciente durante una cita (una fila por medicamento recetado).
     * Guarda la dosis, la frecuencia, la duración y las indicaciones.
     */
    public function up(): void
    {
        Schema::create('prescripcion', function (Blueprint $table) {
            $table->id();

            // Cita que originó la prescripción (opcional)
            $table->unsignedBigInteger('id_cita')->nullable();

            // Paciente (usuario) al que se le formula
            $table->unsignedInteger('cedula_paciente');

            // Médico que formula
            $table->unsignedInteger('cedula_medico');

            // Medicamento formulado (nombre, tomado del catálogo o escrito)
            $table->string('medicamento', 150);

            $table->string('dosis', 100)->nullable();
            $table->string('frecuencia', 100)->nullable();
            $table->string('duracion', 100)->nullable();
            $table->text('indicaciones')->nullable();

            $table->date('fecha');

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('id_cita')
                  ->references('id')->on('cita')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('cedula_paciente')
                  ->references('cedula')->on('usuarios')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('cedula_medico')
                  ->references('cedula')->on('medico')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescripcion');
    }
};
