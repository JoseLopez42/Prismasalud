<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_ingreso_medico');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_ingreso_users');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_ingreso_usuarios');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_log_ingreso_medico
AFTER UPDATE ON medico
FOR EACH ROW
BEGIN
  IF NEW.ultimo_ingreso IS NOT NULL
     AND NOT (NEW.ultimo_ingreso <=> OLD.ultimo_ingreso) THEN
    INSERT INTO log_ingresos (cedula, tipo_usuario, fecha, hora)
    VALUES (
      NEW.cedula,
      'Medico',
      DATE(NEW.ultimo_ingreso),
      TIME(NEW.ultimo_ingreso)
    );
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_log_ingreso_users
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
  IF NEW.ultimo_ingreso IS NOT NULL
     AND NOT (NEW.ultimo_ingreso <=> OLD.ultimo_ingreso) THEN
    INSERT INTO log_ingresos (cedula, tipo_usuario, fecha, hora)
    VALUES (
      NEW.id,
      NEW.rol,
      DATE(NEW.ultimo_ingreso),
      TIME(NEW.ultimo_ingreso)
    );
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_log_ingreso_usuarios
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
  IF NEW.ultimo_ingreso IS NOT NULL
     AND NOT (NEW.ultimo_ingreso <=> OLD.ultimo_ingreso) THEN
    INSERT INTO log_ingresos (cedula, tipo_usuario, fecha, hora)
    VALUES (
      NEW.cedula,
      IFNULL((SELECT nombre FROM tipos_usuario WHERE id = NEW.tipo_usuario_id), 'Sin tipo'),
      DATE(NEW.ultimo_ingreso),
      TIME(NEW.ultimo_ingreso)
    );
  END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_ingreso_medico');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_ingreso_users');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_ingreso_usuarios');
    }
};
