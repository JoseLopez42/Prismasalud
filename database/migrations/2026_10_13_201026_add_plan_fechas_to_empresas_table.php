<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (! Schema::hasColumn('empresas', 'plan')) {
                $table->string('plan', 50)->nullable()->after('direccion');
            }
            if (! Schema::hasColumn('empresas', 'fecha_compra')) {
                $table->date('fecha_compra')->nullable()->after('plan');
            }
            if (! Schema::hasColumn('empresas', 'fecha_expiracion')) {
                $table->date('fecha_expiracion')->nullable()->after('fecha_compra');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            foreach (['plan', 'fecha_compra', 'fecha_expiracion'] as $col) {
                if (Schema::hasColumn('empresas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
