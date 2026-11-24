<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transacciones')) {
            return;
        }

        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licencia_id')->constrained('licencias')->cascadeOnDelete();
            $table->unsignedInteger('usuario_cedula')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('planes')->nullOnDelete();
            $table->unsignedBigInteger('monto')->default(0);
            $table->string('tipo')->default('compra'); // compra | renovacion
            $table->string('metodo_pago')->default('tarjeta_simulada');
            $table->string('referencia_pago')->nullable(); // ej: **** **** **** 1111
            $table->string('estado')->default('aprobado');
            $table->timestamps();

            $table->foreign('usuario_cedula')->references('cedula')->on('usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};