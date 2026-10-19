<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afiliacion', function (Blueprint $table) {
            // Identificación
            $table->unsignedBigInteger('id_tipo_documento')->nullable()->after('id_estado');
            $table->string('pais_expedicion', 100)->nullable()->after('id_tipo_documento');

            // Demográficos
            $table->string('genero', 20)->nullable()->after('pais_expedicion'); // Masculino, Femenino, Otro
            $table->string('estado_civil', 50)->nullable()->after('genero');
            $table->string('grupo_etnico', 50)->nullable()->after('estado_civil');
            $table->boolean('tiene_discapacidad')->default(false)->after('grupo_etnico');
            $table->string('tipo_discapacidad', 100)->nullable()->after('tiene_discapacidad');
            $table->string('nacionalidad', 100)->nullable()->after('tipo_discapacidad');
            $table->string('pais_nacimiento', 100)->nullable()->after('nacionalidad');

            // Residencia
            $table->string('zona_residencia', 20)->nullable()->after('pais_nacimiento'); // Urbana, Rural
            $table->string('telefono_fijo', 20)->nullable()->after('zona_residencia');

            // Socioeconómico (sin ingresos ni NIT empleador)
            $table->unsignedTinyInteger('estrato')->nullable()->after('telefono_fijo'); // 1-6
            $table->string('nivel_educativo', 50)->nullable()->after('estrato');
            $table->string('ocupacion', 100)->nullable()->after('nivel_educativo');

            // Salud (sin RH, sin alergias, sin embarazo)
            $table->string('tipo_sangre', 5)->nullable()->after('ocupacion'); // O+, O-, A+, A-, B+, B-, AB+, AB-
            $table->boolean('victima_conflicto_armado')->default(false)->after('tipo_sangre');

            // Afiliación
            $table->string('numero_afiliacion_anterior', 50)->nullable()->after('victima_conflicto_armado');

            // Foránea a tipo_documento
            $table->foreign('id_tipo_documento')
                  ->references('id')->on('tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::table('afiliacion', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_documento']);
            $table->dropColumn([
                'id_tipo_documento', 'pais_expedicion',
                'genero', 'estado_civil', 'grupo_etnico', 'tiene_discapacidad', 'tipo_discapacidad',
                'nacionalidad', 'pais_nacimiento', 'zona_residencia', 'telefono_fijo',
                'estrato', 'nivel_educativo', 'ocupacion', 'tipo_sangre', 'victima_conflicto_armado',
                'numero_afiliacion_anterior',
            ]);
        });
    }
};
