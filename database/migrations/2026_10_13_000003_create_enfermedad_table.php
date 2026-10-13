<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "enfermedad": enfermedades asociadas a un paciente,
     * registradas por el médico y ligadas (opcionalmente) a una
     * historia clínica.
     */
    public function up(): void
    {
        Schema::create('enfermedad', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('cedula_paciente');
            $table->unsignedInteger('cedula_medico');

            $table->unsignedBigInteger('id_historia_clinica')->nullable();

            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();

            $table->date('fecha');

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

            $table->foreign('id_historia_clinica')
                  ->references('id')->on('historia_clinica')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enfermedad');
    }
};
