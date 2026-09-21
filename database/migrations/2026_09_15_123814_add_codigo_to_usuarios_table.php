<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega a "usuarios" los campos necesarios para recuperar
     * contraseña:
     * - codigo: el código temporal que se envía por correo.
     * - codigo_expira_en: fecha/hora límite de validez del código
     *   (para que no se pueda usar un código viejo).
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('codigo', 6)->nullable()->after('contrasena');
            $table->timestamp('codigo_expira_en')->nullable()->after('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'codigo_expira_en']);
        });
    }
};