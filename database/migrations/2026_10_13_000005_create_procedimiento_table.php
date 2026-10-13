<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "procedimiento": procedimiento que un médico le realiza a
     * un paciente (normalmente a partir de una cita programada).
     */
    public function up(): void
    {
        Schema::create('procedimiento', function (Blueprint $table) {
            $table->id();

            // Paciente (usuario) al que se le practica el procedimiento
            $table->unsignedInteger('cedula_paciente');

            // Médico que lo realiza
            $table->unsignedInteger('cedula_medico');

            // Cita programada que originó el procedimiento (opcional)
            $table->unsignedBigInteger('id_cita')->nullable();

            // Tipo de procedimiento del catálogo (opcional)
            $table->unsignedBigInteger('id_tipo_procedimiento')->nullable();

            $table->date('fecha');
            $table->string('descripcion', 255);
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

            $table->foreign('id_tipo_procedimiento')
                  ->references('id')->on('tipo_procedimiento')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimiento');
    }
};
