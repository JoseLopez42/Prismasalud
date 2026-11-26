# DBA-09 — Ciclo de vida de bitácoras y fechas

Responsable: Paula Ruiz · Fecha: 25/11/2026 · Estado: Implementado en entorno local (XAMPP, MariaDB 10.4, base `unoa`)

Este documento cierra el ciclo de vida de los datos temporales: se eliminó el tipo `TIMESTAMP`, se archivaron las bitácoras vencidas y se dejó la **edad como dato calculado**, no almacenado.

## Reglas generales

- Todo cambio de esquema va en una migración Laravel con `up()` y `down()` funcionales.
- Las migraciones se ejecutan **una por una**, con la conexión de migraciones (`root`):

      php artisan migrate --database=migraciones --path=database/migrations/NOMBRE.php

- La aplicación sigue entrando con `prisma_app` (solo `SELECT, INSERT, UPDATE, DELETE`).
- Antes de cada migración que moviera o borrara datos se tomó un respaldo lógico con `mysqldump --single-transaction --routines --triggers --events --master-data=2`.
- No se usa ningún `EVENT` de MariaDB: la purga se programa desde Laravel.

## Parte A — Fin de `TIMESTAMP`

Migración `2026_11_25_173302_convertir_timestamp_a_datetime`.

Las columnas `TIMESTAMP` de `unoa` se convirtieron a `DATETIME`. Así se evita que MariaDB convierta automáticamente valores a la zona horaria del servidor y se comporte distinto según la hora del día. Después de la migración no queda ninguna columna `TIMESTAMP` en la base.

## Parte B — Archivado y purga de bitácoras

### B1. Tablas históricas

Migración `2026_11_25_173855_crear_tablas_historicas_bitacoras`:

| Tabla activa | Tabla histórica | Columnas |
|---|---|---|
| `log_ingresos` | `log_ingresos_historico` | Iguales que la activa, más `id` propio |
| `log_licencias` | `log_licencias_historico` | Iguales que la activa, más `id` propio |

Las tablas históricas se crearon vacías. `down()` las elimina.

### B2. Comando de purga

`app/Console/Commands/PurgarBitacoras.php`, firma:

    php artisan bitacoras:purgar [--simular]

Plazos de retención:

| Origen | Plazo | Campo usado |
|---|---|---|
| `sessions` | 30 días | `last_activity` (Unix) |
| `log_ingresos` | 1 año | `fecha` |
| `log_licencias` | 2 años | `fecha_cambio` |

Cada lote se mueve a su tabla histórica y se borra de la activa dentro de una transacción. `--simular` solo cuenta y no escribe. Al final imprime un resumen por tabla.

### B3. Programación

`routes/console.php`:

    Schedule::command('bitacoras:purgar')->dailyAt('03:15');

`php artisan schedule:list` confirma `15 3 * * *`. El servidor debe tener configurado el cron de Laravel (`* * * * * ... schedule:run`).

### B4. Prueba de la purga

Respaldo previo:

    mysqldump --defaults-extra-file=C:\scripts_respaldo\prisma_backup.cnf --single-transaction --routines --triggers --events --master-data=2 unoa > C:\respaldos_unoa\unoa_antes_dba09_b.sql

Fila de prueba insertada en `log_ingresos`: cédula `99999999`, `fecha = 2024-11-25`, `hora = 10:00:00`.

Simulación:

    php artisan bitacoras:purgar --simular

```
Modo simulación: NO se borrará ni moverá nada.
sessions ................ Borradas 0
log_ingresos ............ Movidas a histórico 1
log_licencias ........... Movidas a histórico 0
Resumen: 1 registro pendiente (simulado). Base sin cambios.
```

Ejecución real:

```
sessions ................ Borradas 0
log_ingresos ............ Movidas a histórico 1
log_licencias ........... Movidas a histórico 0
```

Verificación:

| Consulta | Resultado |
|---|---|
| `SELECT COUNT(*) FROM log_ingresos WHERE cedula = 99999999` | 0 |
| `SELECT COUNT(*) FROM log_ingresos_historico WHERE cedula = 99999999` | 1 |
| `SELECT MIN(fecha) FROM log_ingresos_historico WHERE cedula = 99999999` | `2024-11-25` |
| `SELECT COUNT(*) FROM log_ingresos` | 15 activas |
| `SELECT COUNT(*) FROM log_ingresos_historico` | 1 histórica |

Una simulación posterior devolvió 0 filas pendientes.

## Parte C — La edad deja de guardarse

### C1. Situación encontrada

`git grep -n -w "edad" -- app resources` mostró interfaces y validaciones que pedían la edad como dato escrito, además de comparaciones que intentaban adivinarla desde la fecha. No hay seeders ni factories que usen `edad`. Las migraciones antiguas que crearon la columna se dejaron intactas (no se reescribe historia).

### C2. Migración

Migración `2026_11_25_180045_quitar_columnas_edad`.

- `up()`: elimina `beneficiarios.edad` y `usuarios.edad`.
- `down()`: vuelve a crear ambas columnas y las llena con `TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())`. En `usuarios`, la fecha se toma del beneficiario con la misma cédula (`usuarios.cedula = beneficiarios.cc_afiliado`).

Respaldo previo (ejecutado y confirmado antes de migrar):

    mysqldump --defaults-extra-file=C:\scripts_respaldo\prisma_backup.cnf --single-transaction --routines --triggers --events --master-data=2 unoa > C:\respaldos_unoa\unoa_antes_dba09_c.sql

Verificación posterior: `usuarios.edad = NO` y `beneficiarios.edad = NO` en el esquema.

### C3. Dato calculado y formularios

`beneficiarios.fecha_nacimiento` es la única fuente de fecha. Los accessors `getEdadAttribute()` calculan los años con `Carbon::parse(...)->age`:

- `App\Models\Admin\Beneficiario`: usa su propia `fecha_nacimiento`.
- `App\Models\Usuario` y `App\Models\Admin\Usuario`: buscan el beneficiario con la misma cédula mediante la relación `beneficiario()`. Para médicos, farmaceutas, administradores y superadmins no existe esa fecha, así que devuelven `null`.

Todas las vistas muestran `—` cuando la edad es `null`.

Cambios en formularios y controladores:

- Se quitó el campo `Edad` de altas y ediciones de usuarios y beneficiarios.
- Cuando el rol es **Paciente** (o el registro es un beneficiario), el campo obligatorio pasa a ser `fecha_nacimiento` con `required|date|before_or_equal:today|after:1900-01-01`.
- Para administradores, médicos y farmaceutas **no se pide** fecha de nacimiento.
- En `AfiliacionController`, el flujo «nuevo beneficiario» guarda `nuevo_fecha_nacimiento` en `beneficiarios.fecha_nacimiento` y ya no envía `nuevo_edad`.
- Se eliminaron las comparaciones que cotejaban la edad escrita contra la fecha (`abs($edad - $edadReal) > 1`). No existía ninguna regla de negocio por parentesco basada en la edad: las reglas de parentesco, cotizante y EPS se conservaron.
- Se quitó el import de `Carbon` de los controladores donde ya no se usa.

### Pérdida de datos aceptada

Antes de C2, los valores de `edad` no coincidían con una fecha en:

- 2 usuarios sin `fecha_nacimiento` consultable.
- 1 beneficiario sin `fecha_nacimiento`.

Esos valores **no se recuperan con `down()`** porque no hay fecha desde la cual recalcularlos. Se acepta la pérdida porque son registros de prueba; el respaldo `unoa_antes_dba09_c.sql` queda como evidencia y punto de restauración manual si se necesitara el valor original.

## Parte D — Verificación final

### C4. Flujo probado

1. Se creó un paciente desde `admin/usuarios/crear` en el navegador (cédula `77777772`, fecha `1985-08-20`). Respuesta `302` a `/admin/usuarios` con el mensaje «Usuario creado correctamente.», sin errores de consola.
2. `beneficiarios` guardó `fecha_nacimiento = 1985-08-20` y el accessor devolvió `41` años.
3. El listado `admin/afiliaciones/beneficiarios` renderizó sin errores: mostró `41 años` para el paciente nuevo, edades calculadas para el resto y `—` para quienes no tienen fecha.
4. La edición de un beneficiario (`30400001`) cargó con fecha `2001-03-15`, sin campo `Edad` y sin errores de consola.
5. `php artisan view:cache` y `php -l` sobre todos los archivos PHP modificados terminaron sin errores. La caché de vistas se limpió después con `php artisan view:clear`.

## Arquitectura resultante

| Dato | Dónde vive | Cómo se obtiene |
|---|---|---|
| Edad | No se almacena | Accessor desde `beneficiarios.fecha_nacimiento` |
| Bitácoras activas | Tablas originales | Consulta normal de la app |
| Bitácoras vencidas | `*_historico` | Comando diario `bitacoras:purgar` |
| Fechas de auditoría | `DATETIME` | Sin conversión automática de zona horaria |

No se hizo `git add`, `commit` ni `push`: los cambios quedan para revisión.
