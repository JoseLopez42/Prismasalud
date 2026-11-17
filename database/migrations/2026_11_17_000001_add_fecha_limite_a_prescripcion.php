<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescripcion', function (Blueprint $table) {
            $table->date('fecha_limite')->nullable()->after('fecha');
            $table->index('fecha_limite');
        });
    }

    public function down(): void
    {
        Schema::table('prescripcion', function (Blueprint $table) {
            $table->dropIndex(['fecha_limite']);
            $table->dropColumn('fecha_limite');
        });
    }
};
