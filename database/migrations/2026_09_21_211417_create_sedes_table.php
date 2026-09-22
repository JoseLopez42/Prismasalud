<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id('id_sede');
            $table->string('nombre_sede');
            $table->string('direccion');
            $table->string('telefono', 20);

            $table->unsignedBigInteger('id_ciudad')->nullable();
            $table->foreign('id_ciudad')
                  ->references('id_ciudad')->on('ciudades')
                  ->onDelete('set null'); // si se borra la ciudad, la sede queda sin ciudad (no se borra)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};