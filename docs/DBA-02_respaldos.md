# DBA-02 — Política de respaldo y restauración (base `unoa`)

Responsable: Paula Ruiz · Fecha: 25/11/2026 · Estado: Implementado en entorno local (XAMPP)

## 1. Qué se configuró
| Componente | Configuración |
|---|---|
| Binlog | `my.ini` [mysqld]: `log_bin=mysql-bin`, `expire_logs_days=14` (archivos en `C:\xampp\mysql\data\`) |
| Copia completa | `mysqldump --single-transaction --routines --triggers --events --master-data=2 unoa` |
| Usuario de respaldo | `prisma_backup@localhost`: solo lectura (SELECT, SHOW VIEW, TRIGGER, LOCK TABLES, EVENT en `unoa`; RELOAD, REPLICATION CLIENT; SELECT en `mysql.proc`) |
| Credenciales | Archivo `C:\scripts_respaldo\prisma_backup.cnf` (fuera del repositorio, nunca se sube a Git) |
| Script | `C:\scripts_respaldo\respaldo_unoa.bat` → genera `C:\respaldos_unoa\unoa_AAAAMMDD.sql` y registra OK/ERROR en `respaldo.log` / `respaldo_errores.log` |
| Programación | Programador de tareas de Windows: "Respaldo diario unoa", diario 7:33 a. m., con ejecución al reanudar si se pierde el inicio programado |

> El usuario `prisma_backup` no se crea por migración de Laravel: es una cuenta del servidor (base interna `mysql`), no parte del esquema de `unoa`, y su contraseña no debe quedar en el repositorio.

## 2. Objetivos
| Indicador | Valor | Cómo se cumple |
|---|---|---|
| RPO | 24 h con la copia diaria; minutos si se conserva el binlog | Copia diaria + binlog desde la posición registrada en la copia (`CHANGE MASTER TO ... MASTER_LOG_POS`) |
| RTO | 4 h | La restauración de prueba tardó menos de 1 minuto con el volumen actual |

## 3. Retención (Resolución 839 de 2017: historia clínica, mínimo 15 años)
- Copias diarias: se conservan 30 días en `C:\respaldos_unoa`.
- Copia mensual (la del día 1): se conserva 15 años, **fuera del equipo** (USB/nube).
- Binlogs: 14 días.

## 4. Prueba de restauración (25/11/2026)
Se restauró `unoa_20261125.sql` en una base aparte (`unoa_restore`) y se comparó con `unoa`:

| Comprobación | unoa | unoa_restore |
|---|---|---|
| Tablas | 54 | 54 |
| Triggers | 4 | 4 |
| historia_clinica | 3 | 3 |
| cita | 9 | 9 |
| prescripcion | 8 | 8 |
| usuarios | 21 | 21 |

Resultado: **exitosa**. Se repite una vez al mes.

## 5. Incidente durante la implementación
Al reiniciar MySQL, la tabla interna `mysql.proxies_priv` estaba dañada (índice `.MAI` de 2.112 KB), por apagados no limpios de XAMPP. `aria_chk -r` no la reconoció; se reemplazó con la copia de fábrica de `mysql\backup\mysql`. Prevención: detener MySQL desde XAMPP y esperar antes de apagar el equipo.

## 6. Pendientes
- Copiar respaldos fuera del equipo (USB o nube).
- Producción (Hostinger, sin SSH): usar los respaldos del panel de Hostinger y exportaciones periódicas; el binlog y el Programador de tareas aplican solo al entorno local.