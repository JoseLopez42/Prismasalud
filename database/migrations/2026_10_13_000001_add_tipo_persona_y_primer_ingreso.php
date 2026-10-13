<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cotizante (titular) o beneficiario (familiar de un cotizante)
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->string('tipo_persona', 15)->default('cotizante')->after('cc_afiliado');
            $table->unsignedInteger('cc_cotizante')->nullable()->after('tipo_persona');
            $table->string('parentesco', 40)->nullable()->after('cc_cotizante');

            $table->foreign('cc_cotizante')
                  ->references('cc_afiliado')->on('beneficiarios')
                  ->nullOnDelete();
        });

        // true = la persona aún no ha creado su contraseña (primer ingreso)
        Schema::table('usuarios', function (Blueprint $table) {
            $table->boolean('debe_crear_contrasena')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropForeign(['cc_cotizante']);
            $table->dropColumn(['tipo_persona', 'cc_cotizante', 'parentesco']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('debe_crear_contrasena');
        });
    }
};