<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de procedimientos que se cargan en los formularios.
     * Cada procedimiento pertenece a un tipo (laboratorio, imágenes, etc.).
     */
    public function up(): void
    {
        Schema::create('catalogo_procedimiento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_tipo_procedimiento')
                ->constrained('tipo_procedimiento')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('nombre', 150)->unique();
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_procedimiento');
    }
};
