<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('usuarios', 'licencia_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('licencia_id')->nullable()->after('tipo_usuario_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'licencia_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('licencia_id');
            });
        }
    }
};
