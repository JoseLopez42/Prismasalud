# DBA-08 — Identidad de personas (decisión de diseño)

**Responsable:** Paula Ruiz
**Fecha:** 2026-11-25
**Estado:** Decidido — se mantiene la cédula como llave por ahora; la migración a `personas` se hace en la fase de código.
**Tipo:** Registro de decisión (ADR). Este punto no incluye migraciones.

---

## 1. Contexto

La auditoría (DBA-08, prioridad "Deseable — iteraciones futuras", tamaño L) señala que la identidad de una persona está repartida en varias tablas y que cada una usa el número de documento como llave primaria:

| Tabla | Llave primaria | Quién guarda |
|---|---|---|
| `usuarios` | `cedula` | pacientes, administradores, farmaceutas |
| `medico` | `cedula` | médicos |
| `beneficiarios` | `cc_afiliado` | afiliados (cotizantes y beneficiarios) |
| `users` | `id` | superadministrador |

Una misma persona puede existir en `usuarios`, `medico` y `beneficiarios` a la vez, con nombres o correos distintos.

### Situación actual

Verificación de solo lectura sobre `unoa` (25/11/2026):

| Dato | Resultado |
|---|---|
| Cédulas presentes a la vez en `usuarios` y `beneficiarios` | 11 |
| Cédulas presentes a la vez en `usuarios` y `medico` | 0 |
| Cédulas presentes en `usuarios`, `beneficiarios` y `medico` | 0 |
| De esas, con `nombres` distinto entre tablas | 0 (0 en `usuarios` vs `beneficiarios`, 0 en `usuarios` vs `medico`, 0 en las tres) |
| `medico.id_tipo_documento` | No existe (confirma R5) |
| `usuarios.id_tipo_documento` | `NULL`: 19 · `1` (Cédula de ciudadanía): 1 · `2` (Tarjeta de identidad): 1 |
| `beneficiarios.id_tipo_documento` | `NULL`: 15 · `2` (Tarjeta de identidad): 1 |

### Relaciones que dependen de la cédula (21)

**Apuntan a `usuarios.cedula`:**
- `cita.cedula_usuario` (ON UPDATE CASCADE)
- `historia_clinica.cedula_paciente` (ON UPDATE CASCADE)
- `diagnostico.cedula_paciente` (ON UPDATE CASCADE)
- `enfermedad.cedula_paciente` (ON UPDATE CASCADE)
- `prescripcion.cedula_paciente` (ON UPDATE CASCADE)
- `procedimiento.cedula_paciente` (ON UPDATE CASCADE)
- `prescripcion.cedula_farmaceuta` (**sin ON UPDATE CASCADE**; `ON DELETE SET NULL`)
- `afiliacion.cc_usuario` (**sin ON UPDATE CASCADE**)
- `transacciones.usuario_cedula` (**sin ON UPDATE CASCADE**; `ON DELETE SET NULL`)
- `empresas.usuario_cedula` (**sin ON UPDATE CASCADE**; `ON DELETE SET NULL`)
- `licencias.usuario_cedula` (**sin ON UPDATE CASCADE**; `ON DELETE SET NULL`)

**Apuntan a `medico.cedula`** (todas con ON UPDATE CASCADE):
- `cita`, `historia_clinica`, `diagnostico`, `enfermedad`, `prescripcion`, `procedimiento`, `horario_medico`

**Apuntan a `beneficiarios.cc_afiliado`:**
- `afiliacion.cc_afiliado` (**sin ON UPDATE CASCADE**)
- `detalle_afiliado_afiliacion.cc_afiliado` (**sin ON UPDATE CASCADE**)
- `beneficiarios.cc_cotizante` (relación con la misma tabla; **sin ON UPDATE CASCADE**)

> Verificación en la base local con:
> `SELECT k.TABLE_NAME, rc.CONSTRAINT_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME, rc.UPDATE_RULE, rc.DELETE_RULE FROM information_schema.REFERENTIAL_CONSTRAINTS rc JOIN information_schema.KEY_COLUMN_USAGE k USING (CONSTRAINT_SCHEMA, CONSTRAINT_NAME, TABLE_NAME) WHERE rc.CONSTRAINT_SCHEMA='unoa' AND rc.REFERENCED_TABLE_NAME IN ('usuarios','medico','beneficiarios') ORDER BY k.REFERENCED_TABLE_NAME, k.TABLE_NAME;`
>
> La consulta sin calificar los campos devuelve en MariaDB `Column 'REFERENCED_TABLE_NAME' in field list is ambiguous`; por eso se usan los alias `rc` y `k`. Devuelve **21 filas**: 13 con `UPDATE_RULE = CASCADE` (7 a `medico` y 6 a `usuarios`) y 8 con `UPDATE_RULE = RESTRICT` (5 a `usuarios` y 3 a `beneficiarios`).

---

## 2. Decisión

**Se mantiene el número de documento como llave primaria en `usuarios`, `medico` y `beneficiarios` mientras dure esta fase.** No se crea la tabla `personas` todavía.

### Por qué no se hace ahora

1. **Crear `personas` sin cambiar el código no resuelve el problema.** Si solo se agrega `persona_id`, los controladores actuales no lo llenan: cada paciente, médico o beneficiario nuevo quedaría con `persona_id` vacío. Habría dos llaves en paralelo y ninguna sería confiable.
2. **El cambio completo toca casi toda la aplicación.** Hay que migrar 21 relaciones y cambiar los controladores, modelos y vistas que usan la cédula. También hay que rehacer el inicio de sesión, que hoy se maneja con `session_start` y variables como `$_SESSION['medico_cedula']` y `$_SESSION['usuario_cedula']`.
3. **Hay trabajo en paralelo sobre las mismas tablas.** José está convirtiendo columnas de `beneficiarios` en catálogos (DBA-06/07) y prepara el diagrama y el diccionario (DBA-10). Cambiar las llaves ahora lo obligaría a repetir ese trabajo.
4. **El riesgo actual es controlado** (ver sección 3). No hay pérdida de datos: en el peor caso, la base rechaza el cambio.

---

## 3. Riesgos que se aceptan por ahora

| # | Riesgo | Qué pasa hoy | Mitigación temporal |
|---|---|---|---|
| R1 | **Corregir una cédula mal digitada** | Si la persona solo tiene citas, historias, diagnósticos, enfermedades, prescripciones como paciente, procedimientos u horarios médicos, `ON UPDATE CASCADE` actualiza todo en una sola operación. InnoDB la hace completa o no la hace, así que no quedan datos a medias. Si tiene **afiliaciones, transacciones, prescripciones como farmaceuta, empresas o licencias** (5 relaciones a `usuarios.cedula`), o cualquiera de las **3 relaciones a `beneficiarios.cc_afiliado`**, MariaDB **rechaza** el cambio: las 8 tienen `UPDATE_RULE = RESTRICT`. | Validar el documento al registrarlo (formulario con confirmación). Si se necesita corregir, hacerlo con una migración o un comando revisado, nunca a mano. |
| R2 | **Pasaporte o cédula de extranjería con el mismo número que una CC** | La llave es solo el número, así que la segunda persona no se puede registrar. | Caso poco frecuente en el contexto actual. Queda resuelto con `UNIQUE(id_tipo_documento, numero_documento)` en la fase de código. |
| R3 | **Cambio de tarjeta de identidad a cédula** (al cumplir 18 años) | Es lo mismo que R1: se cambia el número. | Igual que R1. |
| R4 | **Datos distintos de la misma persona en varias tablas** (nombre, correo) | Una persona puede aparecer con datos diferentes como usuario, médico y beneficiario. | Se acepta. La tabla `personas` lo resuelve guardando nombres y documento en un solo lugar. |
| R5 | **`medico` no tiene tipo de documento** | Se asume que todos los médicos tienen cédula de ciudadanía. | Se agrega en la fase de código. Un médico extranjero con pasaporte hoy no se puede distinguir. |

### Marco normativo (Colombia)

- **Ley 1581 de 2012:** el principio de veracidad o calidad pide que los datos personales sean exactos y completos. Tener la identidad repetida en varias tablas va en contra de ese principio, y por eso la unificación queda planeada.
- **Resolución 839 de 2017:** regula el manejo, la custodia y la conservación de la historia clínica. La historia debe quedar ligada sin ambigüedad al paciente correcto. R1 se mitiga porque la base rechaza o aplica completo cualquier cambio de cédula.
- **Ley 2015 de 2020 (historia clínica electrónica interoperable):** identifica al paciente por **tipo y número** de documento. El diseño final (`UNIQUE(tipo, número)`) se alinea con esta ley.

---

## 4. Plan para la fase de código

Las etapas se hacen **juntas**, en una misma entrega, para no dejar llaves en paralelo.

### Etapa 1 — Tabla `personas`
```sql
CREATE TABLE personas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_tipo_documento BIGINT UNSIGNED NOT NULL,
  numero_documento VARCHAR(20) NOT NULL,
  nombres VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  fecha_nacimiento DATE NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  UNIQUE KEY uq_persona_documento (id_tipo_documento, numero_documento),
  FOREIGN KEY (id_tipo_documento) REFERENCES tipo_documento (id_tipo_documento)
);
```
- Se llena con los datos de `beneficiarios` (la tabla más completa, con fecha de nacimiento), luego `usuarios` y luego `medico`, sin repetir documentos.
- A `medico` se le asigna tipo 1 (Cédula de ciudadanía), salvo que se indique otro.
- Los registros de `usuarios` (19) y `beneficiarios` (15) sin `id_tipo_documento` se revisan antes de migrar; si no hay otro dato, se asigna tipo 1 (Cédula de ciudadanía) y se dejan en un reporte para confirmarlos.
- Antes de la migración se hace un reporte de las personas con nombres distintos entre tablas, para revisarlas a mano.

### Etapa 2 — Relaciones a `persona_id`
- Agregar `persona_id` (NOT NULL, con FK) a `usuarios`, `medico` y `beneficiarios`.
- En las 21 relaciones (13 tablas hijas), crear la columna nueva (por ejemplo, `paciente_id` y `medico_id`), llenarla con un `UPDATE ... JOIN` y luego borrar la columna de cédula y su FK.
- Cambiar modelos (`belongsTo`), controladores y vistas en el mismo cambio.
- Hacer cada paso como una migración con `up()` y `down()`, y con respaldo previo (`unoa_antes_dba08.sql`).

### Etapa 3 — Inicio de sesión unificado
- Unir el acceso de `users`, `usuarios` y `medico` en una sola tabla de cuentas (correo, contraseña, rol, `persona_id`).
- Cambiar el login para que guarde `$_SESSION['persona_id']` y el rol, en lugar de `medico_cedula`, `usuario_cedula` o `superadmin_id`.
- Actualizar el middleware `RegistrarUsuarioAuditoria` para que use `persona_id`.

### Condiciones para empezar
- DBA-06, 07 y 10 terminados y unidos en la rama común.
- Diagrama y diccionario de datos (DBA-10) al día, para usarlos como base del cambio.
- Pruebas manuales de citas, historia clínica, afiliación y compra de licencia antes y después del cambio.

---

## 5. Resultado

- **Cambios en la base:** ninguno.
- **Cambios en el código:** ninguno.
- **Entregable:** este documento.
