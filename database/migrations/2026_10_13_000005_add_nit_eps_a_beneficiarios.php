<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EPS de la persona afiliada. El cotizante la elige; el beneficiario
     * hereda la del cotizante del que es familiar.
     */
    public function up(): void
    {
        if (Schema::hasColumn('beneficiarios', 'nit_eps')) {
            return;
        }

        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->string('nit_eps', 20)->nullable()->after('parentesco');

            $table->foreign('nit_eps')
                  ->references('nit_eps')->on('eps')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropForeign(['nit_eps']);
            $table->dropColumn('nit_eps');
        });
    }
};