# AGENTS.md — Prisma Salud

## Contexto
- Prisma Salud es una plataforma de gestión de citas médicas que se vende a IPS/EPS.
- Stack: Laravel 12 + MariaDB.
- Entornos: XAMPP local y Hostinger en producción; el despliegue se hace por FTP, sin SSH.
- Base de datos: `unoa`.
- Repositorio: rama `master` (principal), rama `paula` (Paula) y rama de José. Los cambios se integran mediante merge a `master`.

## Sesión y roles
- La sesión usa `session_start()` y `$_SESSION` de PHP nativo, por indicación del instructor.
- NO usar el `Auth` de Laravel: ni `Auth::`, ni guards, ni middleware `auth`.
- Claves de sesión: `usuario_cedula`, `tipo_usuario_id`, `medico_cedula`, `superadmin_id`. (`empresa_id` se agregará en REC-01.)
- Roles según `tipo_usuario_id`: `1` Administrador, `2` Paciente, `3` Médico, `4` Farmaceuta.
- El médico inicia sesión desde la tabla `medico` (no desde `usuarios`). El superadmin usa la tabla `users`.

## Base de datos
- Todo cambio de esquema se hace SOLO con migraciones de Laravel que tengan `up()` y `down()` funcionales.
- Las migraciones se ejecutan con: `php artisan migrate --database=migraciones`.
- Nunca editar la base de datos a mano ni modificar migraciones que ya se ejecutaron; crear una nueva.
- Nunca borrar registros clínicos (`historia_clinica`, `diagnostico`, `enfermedad`, `procedimiento`, `prescripcion`, `cita`): usar borrado lógico con la columna `activo = 0`.
- `auditoria_clinica` es de solo lectura: tiene triggers que impiden `UPDATE` y `DELETE`.
- No subir dumps `.sql` al repositorio.

## Seguridad y datos
- No leer, mostrar, copiar ni modificar el archivo `.env`.
- El `APP_KEY` no se puede perder ni regenerar: con él están cifrados los datos sensibles de los beneficiarios. Nunca ejecutar `php artisan key:generate`.
- Normas de Colombia que aplican: Ley 1581 de 2012 (protección de datos personales), Resolución 839 de 2017 (historia clínica) y Ley 2015 de 2020 (historia clínica electrónica interoperable).
- No escribir datos clínicos ni contraseñas en logs.

## Forma de trabajar
- Antes de cambiar código: proponer un plan (archivos a tocar y qué cambia en cada uno) y esperar confirmación.
- No hacer commit sin confirmación. Los mensajes de commit empiezan con el código de la tarea, por ejemplo: `REC-03: licencia en todas las rutas`.
- Código y comentarios en español, explicando qué hace cada bloque importante.
- Después de cada cambio, decir cómo probarlo (`php artisan test` y/o pasos en el navegador).
- Si un cambio toca `ConsultaMedicaController`, `FarmaciaController` o `UsuarioCitaController`, avisarlo: José también trabaja en esos archivos.
- El entorno local es Windows con PowerShell (`C:\xampp\htdocs\Prismasalud`): los comandos que sugieras deben funcionar en PowerShell.
- No hacer `git push` ni merge: eso lo hace Paula.
