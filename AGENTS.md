# AGENTS.md — Prisma Salud

## Contexto
- Plataforma SaaS de gestión de citas médicas para IPS y EPS de Colombia. Proyecto de aprendices del SENA (ADSO).
- Laravel 12, PHP 8.2, MariaDB 10.4 (XAMPP). Base de datos local: "unoa".
- Rama principal: master. José trabaja en la rama "jose". Nunca trabajar directo en master.

## Reglas obligatorias
1. Sesión con session_start() y $_SESSION (PHP nativo), por indicación del instructor. NO usar Auth de Laravel. Claves: usuario_cedula, tipo_usuario_id, medico_cedula, superadmin_id.
2. Roles por tipo_usuario_id: 1 Administrador, 2 Paciente, 3 Médico, 4 Farmaceuta. El médico inicia sesión desde la tabla "medico".
3. Cambios de base de datos solo como migraciones de Laravel con up() y down(). Se ejecutan con: php artisan migrate --database=migraciones. No editar migraciones ya ejecutadas; crear una nueva.
4. Nunca borrar registros clínicos: usar borrado lógico (columna "activo"). No tocar los triggers de auditoria_clinica ni de historia_clinica.
5. No escribir en logs ni en respuestas datos clínicos, contraseñas, códigos de recuperación, números de tarjeta ni CVV.
6. Respetar normas de Colombia: Ley 1581 de 2012, Resolución 839 de 2017 y Ley 2015 de 2020.
7. No tocar ni mostrar el .env. No subir dumps .sql al repositorio.
8. Las pruebas se corren contra MariaDB, no SQLite.
9. Código comentado en español.

## Forma de trabajar
- Trabajar una sola tarea (REC-XX) a la vez. No cambiar nada fuera de esa tarea.
- Antes de cambiar código: proponer un plan con los archivos a tocar y esperar la confirmación de José.
- Al terminar: resumir qué cambió y cómo probarlo.
- No hacer commit, push ni merge sin que José lo confirme. Formato del commit: "REC-XX: descripción en español".
