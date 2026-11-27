<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REC-02: el código de recuperación deja de guardarse en texto plano.
     * Un hash bcrypt ocupa unos 60 caracteres, por eso "codigo" pasa de
     * varchar(6) a varchar(255). "codigo_intentos" cuenta los fallos para
     * invalidar el código después de 5 intentos.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('codigo', 255)->nullable()->change();
            $table->unsignedTinyInteger('codigo_intentos')
                  ->default(0)
                  ->after('codigo_expira_en');
        });
    }

    public function down(): void
    {
        // Un hash no se puede convertir en el código de 6 dígitos original:
        // al volver a varchar(6) se truncaría y quedaría inservible. Por eso
        // se descartan los códigos pendientes (hay que pedir uno nuevo) y
        // luego sí se restaura la forma anterior de las columnas.
        DB::table('usuarios')->update([
            'codigo' => null,
            'codigo_expira_en' => null,
            'codigo_intentos' => 0,
        ]);

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('codigo_intentos');
            $table->string('codigo', 6)->nullable()->change();
        });
    }
};
