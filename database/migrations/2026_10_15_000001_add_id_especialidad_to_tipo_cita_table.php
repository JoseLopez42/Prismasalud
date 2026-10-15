<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipo_cita', function (Blueprint $table) {
            $table->unsignedBigInteger('id_especialidad')->nullable()->after('duracion_minutos');
            $table->foreign('id_especialidad')->references('id')->on('especialidad')
                  ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_cita', function (Blueprint $table) {
            $table->dropForeign(['id_especialidad']);
            $table->dropColumn('id_especialidad');
        });
    }
};
