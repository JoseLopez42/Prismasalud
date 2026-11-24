<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabla puede ya existir (la creo a mano el usuario).
        if (! Schema::hasTable('log_licencias')) {
            Schema::create('log_licencias', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('licencia_id');
                $table->string('nombre_ips', 255)->nullable();
                $table->string('plan_anterior', 100)->nullable();
                $table->string('plan_nuevo', 100)->nullable();
                $table->dateTime('fecha_cambio')->useCurrent();
            });
        }

        // El requisito es un unico trigger: registramos los cambios de plan.
        // Se elimina tambien el trigger de insercion que se habia agregado
        // para renovaciones, para dejar solo el permitido.
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_cambio_plan');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_nueva_licencia');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_log_cambio_plan
AFTER UPDATE ON licencias
FOR EACH ROW
BEGIN
    IF NOT (OLD.plan_id <=> NEW.plan_id) THEN
        INSERT INTO log_licencias (licencia_id, plan_anterior, plan_nuevo, fecha_cambio)
        VALUES (NEW.id, OLD.plan, NEW.plan, NOW());
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_cambio_plan');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_nueva_licencia');
    }
};
