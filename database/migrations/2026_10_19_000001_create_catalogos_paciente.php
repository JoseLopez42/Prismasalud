<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Catálogos de los campos adicionales del paciente / afiliación.
     * Antes estaban hardcodeados en los controladores; ahora viven en la BD
     * para que el administrador pueda agregarlos/editarlos sin tocar código.
     */
    public function up(): void
    {
        $ahora = now();

        Schema::create('paises', function (Blueprint $table) {
            $table->id('id_pais');
            $table->string('nombre', 100)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('generos', function (Blueprint $table) {
            $table->id('id_genero');
            $table->string('nombre', 20)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('estados_civiles', function (Blueprint $table) {
            $table->id('id_estado_civil');
            $table->string('nombre', 50)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('grupos_etnicos', function (Blueprint $table) {
            $table->id('id_grupo_etnico');
            $table->string('nombre', 50)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('zonas_residencia', function (Blueprint $table) {
            $table->id('id_zona_residencia');
            $table->string('nombre', 20)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('estratos', function (Blueprint $table) {
            $table->id('id_estrato');
            $table->integer('numero')->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('niveles_educativos', function (Blueprint $table) {
            $table->id('id_nivel_educativo');
            $table->string('nombre', 50)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('ocupaciones', function (Blueprint $table) {
            $table->id('id_ocupacion');
            $table->string('nombre', 100)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        Schema::create('tipos_sangre', function (Blueprint $table) {
            $table->id('id_tipo_sangre');
            $table->string('nombre', 10)->unique();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        // ---------- Seed con los mismos valores que antes estaban hardcodeados ----------

        DB::table('paises')->insertOrIgnore([
            ['nombre' => 'Colombia', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Venezuela', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Estados Unidos', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'España', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'México', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Argentina', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Chile', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Perú', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Ecuador', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Otro', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        DB::table('generos')->insertOrIgnore([
            ['nombre' => 'Masculino', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Femenino', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Otro', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        DB::table('estados_civiles')->insertOrIgnore([
            ['nombre' => 'Soltero', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Casado', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Unión libre', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Divorciado', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Viudo', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        DB::table('grupos_etnicos')->insertOrIgnore([
            ['nombre' => 'Ninguno', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Indígena', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Afrodescendiente', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Raizal', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Palenquero', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Gitano', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Otro', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        DB::table('zonas_residencia')->insertOrIgnore([
            ['nombre' => 'Urbana', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Rural', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        foreach (range(1, 6) as $n) {
            DB::table('estratos')->insertOrIgnore([
                ['numero' => $n, 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ]);
        }

        DB::table('niveles_educativos')->insertOrIgnore([
            ['nombre' => 'Ninguno', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Primaria', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Secundaria', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Técnica', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Tecnológica', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Universitaria', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Posgrado', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        $ocupaciones = [
            'Agricultor', 'Comerciante', 'Conductor', 'Contador', 'Ingeniero', 'Médico',
            'Enfermero', 'Profesor', 'Abogado', 'Técnico', 'Obrero', 'Empleado',
            'Estudiante', 'Ama de casa', 'Pensionado', 'Desempleado', 'Otro',
        ];
        foreach ($ocupaciones as $nombre) {
            DB::table('ocupaciones')->insertOrIgnore([
                ['nombre' => $nombre, 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ]);
        }

        DB::table('tipos_sangre')->insertOrIgnore([
            ['nombre' => 'O+', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'O-', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'A+', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'A-', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'B+', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'B-', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'AB+', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'AB-', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_sangre');
        Schema::dropIfExists('ocupaciones');
        Schema::dropIfExists('niveles_educativos');
        Schema::dropIfExists('estratos');
        Schema::dropIfExists('zonas_residencia');
        Schema::dropIfExists('grupos_etnicos');
        Schema::dropIfExists('estados_civiles');
        Schema::dropIfExists('generos');
        Schema::dropIfExists('paises');
    }
};
