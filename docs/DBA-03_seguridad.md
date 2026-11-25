# DBA-03 — Mínimo privilegio, auditoría clínica y cifrado de datos sensibles

Responsable: Paula Ruiz · Fecha: 25/11/2026 · Estado: Implementado en entorno local (XAMPP)

Normas: Ley 1581 de 2012 (datos personales y sensibles), Ley 2015 de 2020 (historia clínica electrónica: registro de modificaciones), Resolución 839 de 2017 (conservación de la historia clínica).

## Parte 1 — La aplicación no entra como root

| Usuario | Lo usa | Permisos |
|---|---|---|
| `prisma_app@localhost` | La aplicación (todo el día) | `SELECT, INSERT, UPDATE, DELETE` solo sobre `unoa.*` |
| `root` (local) | Solo `php artisan migrate --database=migraciones` | Todos |

- `config/database.php`: conexión nueva `migraciones`. Toma `DB_MIGRATE_USERNAME` / `DB_MIGRATE_PASSWORD` y, si no existen, usa `DB_USERNAME` / `DB_PASSWORD`.
- `.env.example`: actualizado con `DB_DATABASE=unoa`, `DB_USERNAME=prisma_app` y las variables `DB_MIGRATE_*` (sin contraseñas).
- **Todas las migraciones se ejecutan con:** `php artisan migrate --database=migraciones`

Crear el usuario en cada base local (no va por migración: es una cuenta del servidor, no parte del esquema, y su contraseña no debe quedar en Git):

    CREATE USER 'prisma_app'@'localhost' IDENTIFIED BY '<clave>';
    GRANT SELECT, INSERT, UPDATE, DELETE ON unoa.* TO 'prisma_app'@'localhost';

**Pruebas:** la app funciona con `prisma_app`; `CREATE TABLE` con `prisma_app` devuelve `ERROR 1142 ... CREATE command denied`.

## Parte 2 — Auditoría de la historia clínica

Migración `2026_11_25_160000_create_auditoria_clinica_table`:

| Objeto | Qué hace |
|---|---|
| Tabla `auditoria_clinica` | Guarda tabla, id del registro, acción (UPDATE/DELETE), la fila completa **antes** del cambio (JSON), usuario de BD, cédula del usuario de la app y fecha |
| `trg_auditoria_hc_update` / `trg_auditoria_hc_delete` | En cada UPDATE o DELETE de `historia_clinica` copian la versión anterior |
| `trg_auditoria_clinica_no_update` / `trg_auditoria_clinica_no_delete` | Rechazan cualquier UPDATE o DELETE sobre `auditoria_clinica` (`SIGNAL SQLSTATE '45000'`): el rastro no se puede alterar |

Middleware `RegistrarUsuarioAuditoria` (registrado en `bootstrap/app.php` para todas las rutas web): antes de cada petición lee la cédula de la sesión (`session_start`: `medico_cedula`, `usuario_cedula` o `superadmin_id`) y hace `SET @usuario_app = ?`. Los triggers la guardan en `usuario_app`.

**Pruebas:** al editar una historia desde el panel del médico quedó `usuario_bd = prisma_app@localhost` y `usuario_app = 1032456789`; `DELETE FROM auditoria_clinica` devuelve `ERROR 1644 ... es de solo lectura`.

## Parte 3 — Cifrado de datos sensibles

Migración `2026_11_25_170039_cifrar_datos_sensibles_beneficiarios`: 8 columnas de `beneficiarios` pasan a `TEXT` y los datos existentes se cifran con `APP_KEY` (AES-256, dentro de una transacción). El `down()` descifra y devuelve los tipos originales.

| Columna | Cast en `App\Models\Admin\Beneficiario` |
|---|---|
| `grupo_etnico`, `tipo_sangre`, `tipo_discapacidad`, `telefono`, `telefono_fijo`, `direccion` | `encrypted` |
| `tiene_discapacidad`, `victima_conflicto_armado` | `App\Casts\BooleanoCifrado` (guarda '1'/'0' cifrado y devuelve true/false) |

Acordado con José (DBA-07): los datos de catálogo no sensibles (género, estado civil, nivel educativo, ocupación, países, zona, parentesco) son FK a catálogos; los sensibles se cifran. `correo` no se cifra porque se usa para buscar.

**Pruebas:** en MariaDB los valores se ven como `eyJpdiI6...`; en la app (Afiliaciones → Beneficiarios → Editar) se ven normales; al guardar se vuelven a cifrar.

**Limitaciones:** no se puede filtrar, ordenar ni poner índices o CHECK sobre esas columnas en SQL. Todo acceso debe pasar por el modelo `Beneficiario` (verificado: no hay `DB::table('beneficiarios')` en el código).

> ⚠️ **Si se pierde o cambia `APP_KEY`, los datos cifrados no se pueden recuperar.** Cada base local queda cifrada con el `APP_KEY` de su propio `.env`.

## Pendientes para producción (Hostinger)

- Usuario de base de datos con permisos limitados, creado desde el panel de Hostinger, con contraseña fuerte y distinta a la local.
- Guardar el `APP_KEY` de producción aparte y protegido (como los respaldos). Nunca regenerarlo.
- Cifrado en reposo de InnoDB y conexión TLS a la base: dependen del proveedor.
- Los triggers quedan con `DEFINER` del usuario que ejecute las migraciones.
- `auditoria_clinica` cubre hoy `historia_clinica`; extender a `diagnostico`, `prescripcion` y `procedimiento` si se requiere.