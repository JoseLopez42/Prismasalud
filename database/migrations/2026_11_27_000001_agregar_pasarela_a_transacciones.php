<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transacciones') || Schema::hasColumn('transacciones', 'pasarela')) {
            return;
        }

        Schema::table('transacciones', function (Blueprint $table) {
            // REC-05: las filas existentes quedan como simuladas; referencia_pago no se toca.
            $table->string('pasarela', 30)->default('simulada')->after('metodo_pago');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('transacciones') && Schema::hasColumn('transacciones', 'pasarela')) {
            Schema::table('transacciones', function (Blueprint $table) {
                $table->dropColumn('pasarela');
            });
        }
    }
};
