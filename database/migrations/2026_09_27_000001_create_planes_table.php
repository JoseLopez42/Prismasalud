<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La BD puede venir de otra rama donde la tabla ya existe.
        if (Schema::hasTable('planes')) {
            return;
        }

        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('etiqueta')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('precio_total')->nullable();
            $table->string('periodo')->nullable();
            $table->text('ideal')->nullable();
            $table->json('beneficios')->nullable();
            $table->json('no_incluye')->nullable();
            $table->string('cta')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('icono')->nullable();
            $table->string('clase_icono')->nullable();
            $table->boolean('destacada')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};