<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'ultimo_ingreso')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dateTime('ultimo_ingreso')->nullable();
            });
        }

        if (! Schema::hasColumn('medico', 'ultimo_ingreso')) {
            Schema::table('medico', function (Blueprint $table) {
                $table->dateTime('ultimo_ingreso')->nullable();
            });
        }

        if (! Schema::hasColumn('usuarios', 'ultimo_ingreso')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dateTime('ultimo_ingreso')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'ultimo_ingreso')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('ultimo_ingreso');
            });
        }

        if (Schema::hasColumn('medico', 'ultimo_ingreso')) {
            Schema::table('medico', function (Blueprint $table) {
                $table->dropColumn('ultimo_ingreso');
            });
        }

        if (Schema::hasColumn('usuarios', 'ultimo_ingreso')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('ultimo_ingreso');
            });
        }
    }
};
