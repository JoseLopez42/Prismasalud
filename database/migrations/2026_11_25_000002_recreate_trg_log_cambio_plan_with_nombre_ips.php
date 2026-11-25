<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_cambio_plan');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_log_cambio_plan
AFTER UPDATE ON licencias
FOR EACH ROW
BEGIN
    IF NOT (OLD.plan_id <=> NEW.plan_id) THEN
        INSERT INTO log_licencias (licencia_id, nombre_ips, plan_anterior, plan_nuevo, fecha_cambio)
        VALUES (NEW.id, NEW.nombre_ips, OLD.plan, NEW.plan, NOW());
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_cambio_plan');

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
};
