<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inventario del medicamento. El administrador crea el medicamento
     * con stock 0 y el farmaceuta registra cuántas unidades hay.
     */
    public function up(): void
    {
        Schema::table('medicamento', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('medicamento', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
