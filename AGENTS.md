# AGENTS.md — Prisma Salud

## Contexto
- Prisma Salud es una plataforma de gestión de citas médicas que se vende a IPS/EPS.
- Stack: Laravel 12 + MariaDB.
- Entornos: XAMPP local y Hostinger en producción; el despliegue se hace por FTP, sin SSH.
- Base de datos: `unoa` (en algunos equipos locales la base se llama `unoa_pruebas`; la define cada `.env`).
- Repositorio: rama `master` (principal), rama `paula` (Paula) y rama `jose` (José). Los cambios se integran mediante merge a `master`. Nunca trabajar directo en `master`.

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
- `auditoria_clinica` es de solo lectura: tiene triggers que impiden `UPDATE` y `DELETE`. Tampoco tocar los triggers de auditoría de `historia_clinica`.
- No subir dumps `.sql` al repositorio.

## Seguridad y datos
- No leer, mostrar, copiar ni modificar el archivo `.env`.
- El `APP_KEY` no se puede perder ni regenerar: con él están cifrados los datos sensibles de los beneficiarios. Nunca ejecutar `php artisan key:generate`.
- Normas de Colombia que aplican: Ley 1581 de 2012 (protección de datos personales), Resolución 839 de 2017 (historia clínica) y Ley 2015 de 2020 (historia clínica electrónica interoperable).
- No escribir datos clínicos, contraseñas, códigos de recuperación, números de tarjeta ni CVV en logs ni en respuestas.
- Pagos: el servidor nunca recibe datos de tarjeta (REC-05). Todo pago pasa por la interfaz `App\Services\Pagos\PasarelaPago`.

## Pruebas
- Las pruebas se corren contra MariaDB, no contra SQLite: las migraciones y el middleware de auditoría usan sintaxis de MySQL/MariaDB.

## Forma de trabajar
- Trabajar una sola tarea (REC-XX) a la vez y no cambiar nada fuera de esa tarea.
- Antes de cambiar código: proponer un plan (archivos a tocar y qué cambia en cada uno) y esperar confirmación.
- No hacer commit sin confirmación. Los mensajes de commit empiezan con el código de la tarea, por ejemplo: `REC-03: licencia en todas las rutas`. Para documentación o configuración que no es de una REC se usa `DOC:` o `SETUP:`.
- Código y comentarios en español, explicando qué hace cada bloque importante.
- Después de cada cambio, decir cómo probarlo (`php artisan test` y/o pasos en el navegador).
- Si un cambio toca `ConsultaMedicaController`, `FarmaciaController` o `UsuarioCitaController`, avisarlo: Paula y José trabajan en esos archivos.
- El entorno local es Windows con PowerShell (carpeta del proyecto en `C:\xampp\htdocs\`): los comandos que sugieras deben funcionar en PowerShell.
- El agente nunca hace `git push` ni merge: eso lo hacen Paula o José a mano.