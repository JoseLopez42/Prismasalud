<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Régimen de afiliación al sistema de salud colombiano
     * (ej: Contributivo, Subsidiado).
     */
    public function up(): void
    {
        Schema::create('regimen', function (Blueprint $table) {
            $table->id('id_regimen');
            $table->string('nombre_regimen');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regimen');
    }
};