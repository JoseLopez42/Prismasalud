<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La afiliación queda atada a la EMPRESA (la IPS del administrador), que
     * ya está registrada con su NIT: no hay que escribirla a mano.
     *
     * - nit_empresa / nombre_empresa: datos de la empresa que hace la afiliación.
     * - nit_eps pasa a ser opcional: si la persona no tiene EPS asignada, la
     *   afiliación se identifica con el NIT de la empresa.
     */
    public function up(): void
    {
        Schema::table('afiliacion', function (Blueprint $table) {
            $table->string('nit_empresa', 30)->nullable()->after('nit_eps');
            $table->string('nombre_empresa', 150)->nullable()->after('nit_empresa');
        });

        // nit_eps es clave foránea: hay que soltarla antes de volverla opcional.
        Schema::table('afiliacion', function (Blueprint $table) {
            $table->dropForeign(['nit_eps']);
        });

        Schema::table('afiliacion', function (Blueprint $table) {
            $table->string('nit_eps', 20)->nullable()->change();
        });

        Schema::table('afiliacion', function (Blueprint $table) {
            $table->foreign('nit_eps')
                  ->references('nit_eps')->on('eps')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('afiliacion', function (Blueprint $table) {
            $table->dropForeign(['nit_eps']);
        });

        Schema::table('afiliacion', function (Blueprint $table) {
            $table->string('nit_eps', 20)->nullable(false)->change();
            $table->dropColumn(['nit_empresa', 'nombre_empresa']);
        });

        Schema::table('afiliacion', function (Blueprint $table) {
            $table->foreign('nit_eps')
                  ->references('nit_eps')->on('eps')
                  ->nullOnDelete();
        });
    }
};
