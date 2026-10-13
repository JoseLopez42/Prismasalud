<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El catálogo de medicamentos cambia su llave primaria: pasa de un
     * "id" autoincremental a "codigo", porque cada medicamento trae su
     * propio código (el que lo identifica). El código lo digita el
     * administrador y debe ser único.
     *
     * Como la tabla es un catálogo de apoyo (sin llaves foráneas que la
     * referencien) y todavía no tiene datos creados por el usuario, se
     * rehace la tabla y se recargan los medicamentos base.
     */
    public function up(): void
    {
        if (Schema::hasColumn('medicamento', 'codigo')) {
            return;
        }

        Schema::dropIfExists('medicamento');

        Schema::create('medicamento', function (Blueprint $table) {
            // Llave primaria: código del medicamento (lo digita el admin)
            $table->string('codigo', 50)->primary();

            $table->string('nombre', 150)->unique();
            $table->string('presentacion', 100)->nullable();
            $table->text('descripcion')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });

        $ahora = now();

        DB::table('medicamento')->insert([
            ['codigo' => 'ACE-500', 'nombre' => 'Acetaminofén', 'presentacion' => 'Tableta 500 mg', 'descripcion' => 'Analgésico y antipirético.',          'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'IBU-400', 'nombre' => 'Ibuprofeno',   'presentacion' => 'Tableta 400 mg', 'descripcion' => 'Antiinflamatorio no esteroideo.',     'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'AMO-500', 'nombre' => 'Amoxicilina',  'presentacion' => 'Cápsula 500 mg', 'descripcion' => 'Antibiótico de amplio espectro.',     'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'LOR-10',  'nombre' => 'Loratadina',   'presentacion' => 'Tableta 10 mg',  'descripcion' => 'Antihistamínico.',                    'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'OME-20',  'nombre' => 'Omeprazol',    'presentacion' => 'Cápsula 20 mg',  'descripcion' => 'Inhibidor de la bomba de protones.',  'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'DIC-50',  'nombre' => 'Diclofenaco',  'presentacion' => 'Tableta 50 mg',  'descripcion' => 'Antiinflamatorio y analgésico.',      'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'MET-850', 'nombre' => 'Metformina',   'presentacion' => 'Tableta 850 mg', 'descripcion' => 'Antidiabético oral.',                 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['codigo' => 'LOS-50',  'nombre' => 'Losartán',     'presentacion' => 'Tableta 50 mg',  'descripcion' => 'Antihipertensivo.',                   'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamento');

        Schema::create('medicamento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->string('presentacion', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $ahora = now();

        DB::table('medicamento')->insert([
            ['nombre' => 'Acetaminofén', 'presentacion' => 'Tableta 500 mg',  'descripcion' => 'Analgésico y antipirético.',           'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Ibuprofeno',   'presentacion' => 'Tableta 400 mg',  'descripcion' => 'Antiinflamatorio no esteroideo.',      'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Amoxicilina',  'presentacion' => 'Cápsula 500 mg',  'descripcion' => 'Antibiótico de amplio espectro.',      'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Loratadina',   'presentacion' => 'Tableta 10 mg',   'descripcion' => 'Antihistamínico.',                     'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Omeprazol',    'presentacion' => 'Cápsula 20 mg',   'descripcion' => 'Inhibidor de la bomba de protones.',   'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Diclofenaco',  'presentacion' => 'Tableta 50 mg',   'descripcion' => 'Antiinflamatorio y analgésico.',       'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Metformina',   'presentacion' => 'Tableta 850 mg',  'descripcion' => 'Antidiabético oral.',                  'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Losartán',     'presentacion' => 'Tableta 50 mg',   'descripcion' => 'Antihipertensivo.',                    'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }
};
