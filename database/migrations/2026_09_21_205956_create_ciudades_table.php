<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id('id_ciudad');
            $table->string('ciudad');

            $table->unsignedBigInteger('id_departamento')->nullable();
            $table->foreign('id_departamento')
                  ->references('id_departamento')->on('departamentos')
                  ->onDelete('set null'); // si se borra el departamento, la ciudad queda sin departamento (no se borra)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};