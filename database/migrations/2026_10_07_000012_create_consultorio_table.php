<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "consultorio": espacios físicos de la IPS donde se atiende.
     * Puede o no estar ligado a una sede (si se borra la sede,
     * el consultorio queda sin sede, no se borra).
     */
    public function up(): void
    {
        Schema::create('consultorio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->string('ubicacion', 150)->nullable(); // piso, bloque, etc.

            $table->unsignedBigInteger('id_sede')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('id_sede')
                  ->references('id_sede')->on('sedes')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultorio');
    }
};
