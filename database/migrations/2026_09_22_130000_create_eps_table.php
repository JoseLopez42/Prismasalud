<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EPS (Entidades Promotoras de Salud) con las que trabaja
     * PrismaSalud IPS. La PK es el NIT (varchar), como identificador
     * natural de la entidad.
     */
    public function up(): void
    {
        Schema::create('eps', function (Blueprint $table) {
            $table->string('nit_eps', 20)->primary();
            $table->string('nombre_eps');
            $table->string('telefono_contacto', 20);

            $table->unsignedBigInteger('id_sede')->nullable();
            $table->foreign('id_sede')
                  ->references('id_sede')->on('sedes')
                  ->onDelete('set null');

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eps');
    }
};