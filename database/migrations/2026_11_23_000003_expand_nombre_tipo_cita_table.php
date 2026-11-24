<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los nombres oficiales CUPS pueden superar los 100 caracteres.
     * Se amplía el campo conservando su índice único.
     */
    public function up(): void
    {
        Schema::table('tipo_cita', function (Blueprint $table) {
            $table->string('nombre', 191)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tipo_cita', function (Blueprint $table) {
            $table->string('nombre', 100)->change();
        });
    }
};
