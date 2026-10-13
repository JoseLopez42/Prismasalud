<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "medicamento": catálogo de medicamentos que el médico
     * puede formular al paciente. Sirve de apoyo para las prescripciones.
     */
    public function up(): void
    {
        Schema::create('medicamento', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150)->unique();
            $table->string('presentacion', 100)->nullable();
            $table->text('descripcion')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamento');
    }
};
