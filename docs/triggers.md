# Triggers de `unoa_pruebas`

Generado el 26/11/2026 desde `information_schema.TRIGGERS`. Hay 8 triggers: 3 alimentan `log_ingresos`, 1 registra cambios de plan en licencias, 2 auditan `historia_clinica` y 2 protegen `auditoria_clinica` como tabla de solo lectura.

## `trg_log_ingreso_medico`

- **Tabla:** `medico`
- **Momento:** `AFTER UPDATE`
- **Acción:** si `ultimo_ingreso` no es nulo y cambió respecto de `OLD.ultimo_ingreso`, inserta en `log_ingresos` la cédula del médico, el tipo `'Medico'`, la fecha y la hora tomadas de `NEW.ultimo_ingreso`.
- **Uso:** dejar trazabilidad de los accesos de los médicos sin depender de que la aplicación escriba directamente la bitácora.

## `trg_log_ingreso_users`

- **Tabla:** `users`
- **Momento:** `AFTER UPDATE`
- **Acción:** si `ultimo_ingreso` no es nulo y cambió, inserta en `log_ingresos` usando `NEW.id` como identificador, `NEW.rol` como tipo de usuario y la fecha/hora de `NEW.ultimo_ingreso`.
- **Uso:** registrar los ingresos de las cuentas de autenticación Laravel, que se identifican por `id` y no por cédula.

## `trg_log_ingreso_usuarios`

- **Tabla:** `usuarios`
- **Momento:** `AFTER UPDATE`
- **Acción:** si `ultimo_ingreso` no es nulo y cambió, inserta en `log_ingresos` con `NEW.cedula`, el nombre correspondiente de `tipos_usuario` (o `'Sin tipo'` si no existe) y la fecha/hora de `NEW.ultimo_ingreso`.
- **Uso:** conservar el tipo de usuario legible en la bitácora, resuelto dinámicamente desde el catálogo.

## `trg_log_cambio_plan`

- **Tabla:** `licencias`
- **Momento:** `AFTER UPDATE`
- **Acción:** cuando `plan_id` cambia de forma efectiva (`OLD.plan_id <=> NEW.plan_id` es falso), inserta en `log_licencias` el `id` y `nombre_ips` de la licencia, el plan anterior, el plan nuevo y `NOW()`.
- **Uso:** auditar cambios de plan comercial de una licencia, tanto en altas como en renovaciones.

## `trg_auditoria_hc_update`

- **Tabla:** `historia_clinica`
- **Momento:** `AFTER UPDATE`
- **Acción:** inserta en `auditoria_clinica` una fila con `tabla = 'historia_clinica'`, `id_registro = OLD.id`, `accion = 'UPDATE'`, un `JSON_OBJECT` con los valores anteriores (`empresa_id`, `cedula_paciente`, `cedula_medico`, `id_cita`, `fecha`, `motivo_consulta`, `antecedentes`, `examen_fisico`, `observaciones`, `activo`, `created_at`, `updated_at`), `USER()`, `@usuario_app` y `NOW()`.
- **Uso:** guardar la versión previa de cada modificación clínica; la variable `@usuario_app` la puede establecer el middleware Laravel con el usuario de la sesión.

## `trg_auditoria_hc_delete`

- **Tabla:** `historia_clinica`
- **Momento:** `AFTER DELETE`
- **Acción:** inserta en `auditoria_clinica` una fila con `tabla = 'historia_clinica'`, `id_registro = OLD.id`, `accion = 'DELETE'` y el mismo `JSON_OBJECT` de valores anteriores que el trigger de `UPDATE`, más `USER()`, `@usuario_app` y `NOW()`.
- **Uso:** conservar las historias clínicas eliminadas antes de que el dato desaparezca de la tabla activa.

## `trg_auditoria_clinica_no_update`

- **Tabla:** `auditoria_clinica`
- **Momento:** `BEFORE UPDATE`
- **Acción:** lanza `SIGNAL SQLSTATE '45000'` con el mensaje de que `auditoria_clinica` es de solo lectura y no puede modificarse.
- **Uso:** impedir la alteración de evidencia clínica auditable.

## `trg_auditoria_clinica_no_delete`

- **Tabla:** `auditoria_clinica`
- **Momento:** `BEFORE DELETE`
- **Acción:** lanza `SIGNAL SQLSTATE '45000'` con el mensaje de que `auditoria_clinica` es de solo lectura y no puede borrarse.
- **Uso:** impedir la eliminación de evidencia clínica auditable.

## Notas de operación

- Los triggers de `log_ingresos` solo escriben cuando `ultimo_ingreso` cambia; actualizar otras columnas no genera filas nuevas.
- `trg_log_cambio_plan` compara con `<=>`, por lo que distingue correctamente cambios desde o hacia `NULL`.
- Los triggers de auditoría clínica dependen de que la migración que crea `empresa_id` en `historia_clinica` se ejecute antes que la migración que crea `auditoria_clinica`; por eso la migración de auditoría se renombró a `2026_11_25_160000_create_auditoria_clinica_table.php`.
- `log_ingresos` y `log_licencias` tienen tablas históricas para la purga programada por `php artisan bitacoras:purgar`.
