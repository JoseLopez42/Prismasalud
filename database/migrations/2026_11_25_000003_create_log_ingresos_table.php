<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('log_ingresos')) {
            DB::statement('ALTER TABLE log_ingresos MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            DB::statement('ALTER TABLE log_ingresos CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            return;
        }

        Schema::create('log_ingresos', function (Blueprint $table) {
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cedula');
            $table->string('tipo_usuario', 50);
            $table->date('fecha');
            $table->time('hora');
        });
    }

    public function down(): void
    {
        // Tabla de bitácora: no se elimina en rollback para no perder el historial
    }
};
