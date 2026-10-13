<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla "detalle_procedimiento": líneas o resultados que cuelgan
     * de un procedimiento (por ejemplo, exámenes incluidos o hallazgos).
     */
    public function up(): void
    {
        Schema::create('detalle_procedimiento', function (Blueprint $table) {
            $table->id();

            // Procedimiento al que pertenece el detalle
            $table->unsignedBigInteger('id_procedimiento');

            $table->string('descripcion', 255);
            $table->unsignedInteger('cantidad')->default(1);
            $table->text('resultado')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->foreign('id_procedimiento')
                  ->references('id')->on('procedimiento')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_procedimiento');
    }
};
