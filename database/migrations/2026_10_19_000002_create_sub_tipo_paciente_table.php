<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo "sub_tipo_paciente": diferencia al paciente COTIZANTE
     * (titular de la afiliación) del paciente BENEFICIARIO (familiar que
     * depende de un cotizante).
     *
     * - Se asigna "Cotizante" a los pacientes creados desde el módulo de
     *   Usuarios.
     * - Se asigna "Beneficiario" a los pacientes creados desde el módulo
     *   de Afiliación (nuevo beneficiario).
     */
    public function up(): void
    {
        $ahora = now();

        Schema::create('sub_tipo_paciente', function (Blueprint $table) {
            $table->id('id_sub_tipo_paciente');
            $table->string('nombre', 30)->unique();
            $table->string('descripcion', 150)->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        DB::table('sub_tipo_paciente')->insertOrIgnore([
            [
                'nombre'      => 'Cotizante',
                'descripcion' => 'Titular de la afiliación. Se registra desde el módulo de Usuarios.',
                'activo'      => 1,
                'created_at'  => $ahora,
                'updated_at'  => $ahora,
            ],
            [
                'nombre'      => 'Beneficiario',
                'descripcion' => 'Familiar afiliado a un cotizante. Se registra desde el módulo de Afiliación.',
                'activo'      => 1,
                'created_at'  => $ahora,
                'updated_at'  => $ahora,
            ],
        ]);

        // Cada usuario puede tener un sub tipo de paciente (solo aplica a los
        // pacientes; los demás roles lo dejan en NULL).
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_sub_tipo_paciente')
                  ->nullable()
                  ->after('tipo_usuario_id');

            $table->foreign('id_sub_tipo_paciente')
                  ->references('id_sub_tipo_paciente')->on('sub_tipo_paciente')
                  ->nullOnDelete();
        });

        $this->clasificarPacientesExistentes();
    }

    /**
     * Los pacientes que ya existían se clasifican según el "tipo_persona"
     * de su ficha en la tabla "beneficiarios": los que son 'beneficiario'
     * quedan como Paciente Beneficiario y el resto como Paciente Cotizante.
     */
    private function clasificarPacientesExistentes(): void
    {
        $idCotizante    = DB::table('sub_tipo_paciente')->where('nombre', 'Cotizante')->value('id_sub_tipo_paciente');
        $idBeneficiario = DB::table('sub_tipo_paciente')->where('nombre', 'Beneficiario')->value('id_sub_tipo_paciente');
        $idRolPaciente  = DB::table('tipos_usuario')->where('nombre', 'Paciente')->value('id');

        if (! $idCotizante || ! $idBeneficiario || ! $idRolPaciente) {
            return;
        }

        DB::statement(
            'UPDATE usuarios u
             LEFT JOIN beneficiarios b ON b.cc_afiliado = u.cedula
             SET u.id_sub_tipo_paciente = CASE
                    WHEN b.tipo_persona = \'beneficiario\' THEN ' . (int) $idBeneficiario . '
                    ELSE ' . (int) $idCotizante . '
                 END
             WHERE u.tipo_usuario_id = ' . (int) $idRolPaciente
        );
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_sub_tipo_paciente']);
            $table->dropColumn('id_sub_tipo_paciente');
        });

        Schema::dropIfExists('sub_tipo_paciente');
    }
};
