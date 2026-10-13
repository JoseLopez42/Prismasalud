<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de estados de afiliación
        Schema::create('estado', function (Blueprint $table) {
            $table->id('id_estado');
            $table->string('estado', 50);
            $table->timestamps();
        });

        // Beneficiarios (personas afiliadas)
        Schema::create('beneficiarios', function (Blueprint $table) {
            $table->unsignedInteger('cc_afiliado')->primary();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->unsignedInteger('edad');
            $table->string('telefono', 20);
            $table->string('direccion', 255);
            $table->string('correo', 100);
            $table->date('fecha_nacimiento');
            $table->timestamps();
        });

        // Afiliaciones
        Schema::create('afiliacion', function (Blueprint $table) {
            $table->id('id_afiliacion');
            $table->date('fecha_afiliacion');
            $table->string('parentesco', 50);
            $table->unsignedBigInteger('id_tipo_afiliacion');
            $table->string('nit_eps', 20);
            $table->unsignedInteger('cc_usuario');
            $table->unsignedBigInteger('id_estado');
            $table->unsignedInteger('cc_afiliado');
            $table->timestamps();

            $table->foreign('id_tipo_afiliacion')->references('id_regimen')->on('regimen');
            $table->foreign('nit_eps')->references('nit_eps')->on('eps');
            $table->foreign('cc_usuario')->references('cedula')->on('usuarios');
            $table->foreign('id_estado')->references('id_estado')->on('estado');
            $table->foreign('cc_afiliado')->references('cc_afiliado')->on('beneficiarios');
        });

        // Detalle afiliado-afiliación
        Schema::create('detalle_afiliado_afiliacion', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_afiliacion');
            $table->unsignedInteger('cc_afiliado');
            $table->unsignedBigInteger('id_estado');
            $table->timestamps();

            $table->foreign('id_afiliacion')->references('id_afiliacion')->on('afiliacion');
            $table->foreign('cc_afiliado')->references('cc_afiliado')->on('beneficiarios');
            $table->foreign('id_estado')->references('id_estado')->on('estado');
        });

        // Estados básicos por defecto
        \DB::table('estado')->insert([
            ['estado' => 'Activo', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'Inactivo', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'Suspendido', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'Pendiente', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'Bloqueado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_afiliado_afiliacion');
        Schema::dropIfExists('afiliacion');
        Schema::dropIfExists('beneficiarios');
        Schema::dropIfExists('estado');
    }
};
