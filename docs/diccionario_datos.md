# Diccionario de datos — `unoa_pruebas`

Generado el 26/11/2026 desde `information_schema` de `unoa_pruebas`. Complementado con la lectura de modelos, controladores y migraciones de Laravel.

Se documentan 50 tablas de negocio. Se excluyen `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens` y `migrations`.

Los campos sensibles de `beneficiarios` se guardan cifrados y se exponen descifrados mediante los casts de `App\Models\Admin\Beneficiario`.

## `afiliacion`

**Propósito.** Registra la afiliación de un usuario o beneficiario a una EPS, régimen, empresa y estado, con su fecha.

**Llaves foráneas.**

- `cc_afiliado` → `beneficiarios.cc_afiliado` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).
- `cc_usuario` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).
- `id_estado` → `estado.id_estado` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).
- `id_parentesco` → `parentescos.id_parentesco` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_tipo_afiliacion` → `regimen.id_regimen` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).
- `nit_eps` → `eps.nit_eps` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_afiliacion` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Afiliación relacionada con el registro. |
| `fecha_afiliacion` | `date` | No | — | NULL | Fecha en que se formalizó la afiliación. |
| `id_tipo_afiliacion` | `bigint(20) unsigned` | No | INDEX | NULL | Régimen o tipo de afiliación contratado; apunta a regimen.id_regimen. |
| `nit_eps` | `varchar(20)` | Sí | INDEX | `NULL` | NIT de la EPS seleccionada para la afiliación. |
| `nit_empresa` | `varchar(30)` | Sí | — | `NULL` | NIT de la empresa donde cotiza o trabaja el afiliado. |
| `nombre_empresa` | `varchar(150)` | Sí | — | `NULL` | Nombre de la empresa registrada en la afiliación. |
| `cc_usuario` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del usuario que gestiona la afiliación. |
| `id_estado` | `bigint(20) unsigned` | No | INDEX | NULL | Estado relacionado con el registro. |
| `cc_afiliado` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del afiliado o beneficiario vinculado. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
| `id_parentesco` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Parentesco del afiliado respecto del cotizante, cuando aplica. |

## `auditoria_clinica`

**Propósito.** Registro inmutable de cambios y bajas de historia clínica para trazabilidad sanitaria (Ley 2015 de 2020).

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- `valores_antes`: `json_valid(`valores_antes`)`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `tabla` | `varchar(64)` | No | INDEX | NULL | Nombre de la tabla auditada, actualmente historia_clinica. |
| `id_registro` | `bigint(20) unsigned` | No | — | NULL | Identificador del registro auditado dentro de la tabla. |
| `accion` | `enum('UPDATE','DELETE')` | No | — | NULL | Acción registrada: UPDATE o DELETE. |
| `valores_antes` | `longtext` | No | — | NULL | JSON con los valores anteriores del registro modificado o eliminado. |
| `usuario_bd` | `varchar(100)` | No | — | NULL | Usuario de base de datos que ejecutó la acción. |
| `usuario_app` | `bigint(20) unsigned` | Sí | — | `NULL` | Usuario de la aplicación tomado de la variable de sesión @usuario_app. |
| `fecha` | `datetime` | No | — | `current_timestamp()` | Fecha y hora en que ocurrió la acción auditada. |

## `beneficiarios`

**Propósito.** Ficha demográfica y de seguridad social de cotizantes y beneficiarios, con datos sensibles cifrados.

**Llaves foráneas.**

- `cc_cotizante` → `beneficiarios.cc_afiliado` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_estado_civil` → `estados_civiles.id_estado_civil` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_genero` → `generos.id_genero` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_nivel_educativo` → `niveles_educativos.id_nivel_educativo` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_ocupacion` → `ocupaciones.id_ocupacion` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_pais_expedicion` → `paises.id_pais` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_pais_nacimiento` → `paises.id_pais` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_pais_nacionalidad` → `paises.id_pais` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_parentesco` → `parentescos.id_parentesco` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_tipo_documento` → `tipo_documento.id_tipo_documento` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_zona_residencia` → `zonas_residencia.id_zona_residencia` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `nit_eps` → `eps.nit_eps` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- `chk_beneficiarios_cotizante_cc`: ``tipo_persona` = 'cotizante' and `cc_cotizante` is null or `tipo_persona` = 'beneficiario' and `cc_cotizante` is not null`.
- `chk_beneficiarios_estrato`: ``estrato` is null or `estrato` between 1 and 6`.
- `chk_beneficiarios_tipo_persona`: ``tipo_persona` in ('cotizante','beneficiario')`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `cc_afiliado` | `bigint(20) unsigned` | No | PK | NULL | Cédula del cotizante o beneficiario; llave natural de la ficha. |
| `tipo_persona` | `varchar(15)` | No | — | `'cotizante'` | Tipo de persona: cotizante o beneficiario. |
| `cc_cotizante` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cédula del cotizante cuando la ficha corresponde a un beneficiario; nula en cotizantes. |
| `nit_eps` | `varchar(20)` | Sí | INDEX | `NULL` | EPS de la ficha del beneficiario. |
| `id_tipo_documento` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Tipo de documento relacionado con el registro. |
| `grupo_etnico` | `text` | Sí | — | `NULL` | Grupo étnico del beneficiario; dato sensible cifrado. |
| `tiene_discapacidad` | `text` | Sí | — | `NULL` | Indica si la persona tiene discapacidad; dato sensible cifrado. |
| `tipo_discapacidad` | `text` | Sí | — | `NULL` | Tipo de discapacidad declarada; dato sensible cifrado. |
| `telefono_fijo` | `text` | Sí | — | `NULL` | Teléfono fijo del beneficiario; dato sensible cifrado. |
| `estrato` | `tinyint(3) unsigned` | Sí | — | `NULL` | Estrato socioeconómico del beneficiario, entre 1 y 6. |
| `tipo_sangre` | `text` | Sí | — | `NULL` | Tipo de sangre del beneficiario; dato sensible cifrado. |
| `victima_conflicto_armado` | `text` | Sí | — | `NULL` | Indica si la persona es víctima del conflicto armado; dato sensible cifrado. |
| `numero_afiliacion_anterior` | `varchar(50)` | Sí | — | `NULL` | Número de afiliación que la persona tenía antes de la migración. |
| `nombres` | `varchar(100)` | No | — | NULL | Nombres del beneficiario. |
| `apellidos` | `varchar(100)` | No | — | NULL | Apellidos del beneficiario. |
| `telefono` | `text` | Sí | — | `NULL` | Teléfono móvil del beneficiario; dato sensible cifrado. |
| `direccion` | `text` | No | — | NULL | Dirección de residencia del beneficiario; dato sensible cifrado. |
| `correo` | `varchar(100)` | No | — | NULL | Correo electrónico del beneficiario; dato sensible cifrado. |
| `fecha_nacimiento` | `date` | Sí | — | `NULL` | Fecha de nacimiento del beneficiario. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
| `id_genero` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Género del beneficiario; apunta a generos.id_genero. |
| `id_estado_civil` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Estado civil del beneficiario; apunta a estados_civiles.id_estado_civil. |
| `id_nivel_educativo` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Nivel educativo del beneficiario; apunta a niveles_educativos.id_nivel_educativo. |
| `id_ocupacion` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Ocupación del beneficiario; apunta a ocupaciones.id_ocupacion. |
| `id_pais_nacionalidad` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | País de nacionalidad del beneficiario. |
| `id_pais_nacimiento` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | País de nacimiento del beneficiario. |
| `id_pais_expedicion` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | País de expedición del documento de identidad. |
| `id_zona_residencia` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Zona de residencia del beneficiario. |
| `id_parentesco` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Parentesco del beneficiario con el cotizante. |
| `ocupacion_otra` | `varchar(100)` | Sí | — | `NULL` | Descripción libre de la ocupación cuando no corresponde una opción del catálogo. |

## `catalogo_procedimiento`

**Propósito.** Catálogo de procedimientos agrupados por tipo, usado al registrar procedimientos clínicos.

**Llaves foráneas.**

- `id_tipo_procedimiento` → `tipo_procedimiento.id` (`ON DELETE CASCADE`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `id_tipo_procedimiento` | `bigint(20) unsigned` | No | INDEX | NULL | Tipo de procedimiento relacionado con el registro. |
| `nombre` | `varchar(150)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `cita`

**Propósito.** Agenda de citas médicas por empresa, paciente, médico, consultorio, tipo y estado, con su franja horaria.

**Llaves foráneas.**

- `cedula_medico` → `medico.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `cedula_usuario` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_consultorio` → `consultorio.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_estado_cita` → `estado_cita.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_tipo_cita` → `tipo_cita.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- `chk_cita_hora`: ``hora_fin` > `hora_inicio``.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Empresa o IPS a la que pertenece la cita; apunta a empresas.id. |
| `cedula_usuario` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del paciente que solicita la cita. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del médico asignado a la cita. |
| `id_consultorio` | `bigint(20) unsigned` | No | INDEX | NULL | Consultorio donde se atenderá la cita. |
| `id_tipo_cita` | `bigint(20) unsigned` | No | INDEX | NULL | Tipo de cita médica, con duración y código CUPS. |
| `id_estado_cita` | `bigint(20) unsigned` | No | INDEX | NULL | Estado actual de la cita. |
| `fecha` | `date` | No | INDEX | NULL | Fecha en que se programa la cita. |
| `hora_inicio` | `time` | No | — | NULL | Hora de inicio de la cita. |
| `hora_fin` | `time` | No | — | NULL | Hora de fin de la cita; debe ser mayor que hora_inicio. |
| `motivo` | `varchar(255)` | Sí | — | `NULL` | Motivo por el que se agenda la cita. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si la cita permanece activa. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora en que se creó la cita. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `ciudades`

**Propósito.** Catálogo de ciudades asociadas a un departamento.

**Llaves foráneas.**

- `id_departamento` → `departamentos.id_departamento` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_ciudad` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Ciudad relacionada con el registro. |
| `ciudad` | `varchar(255)` | No | — | NULL | Nombre de la ciudad. |
| `id_departamento` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Departamento al que pertenece la ciudad. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `consultorio`

**Propósito.** Consultorios físicos de una sede donde se atienden citas y se programan horarios médicos.

**Llaves foráneas.**

- `id_sede` → `sedes.id_sede` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `nombre` | `varchar(100)` | No | UNIQUE | NULL | Nombre del consultorio. |
| `descripcion` | `varchar(255)` | Sí | — | `NULL` | Descripción del consultorio. |
| `ubicacion` | `varchar(150)` | Sí | — | `NULL` | Ubicación física del consultorio dentro de la sede. |
| `id_sede` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Sede a la que pertenece el consultorio. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `departamentos`

**Propósito.** Catálogo de departamentos del país.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_departamento` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Departamento relacionado con el registro. |
| `nombre_departamento` | `varchar(255)` | No | — | NULL | Nombre del departamento. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `detalle_afiliado_afiliacion`

**Propósito.** Relación entre afiliaciones y afiliados, con el estado de cada vínculo.

**Llaves foráneas.**

- `cc_afiliado` → `beneficiarios.cc_afiliado` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).
- `id_afiliacion` → `afiliacion.id_afiliacion` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).
- `id_estado` → `estado.id_estado` (`ON DELETE RESTRICT`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_detalle` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_detalle. |
| `id_afiliacion` | `bigint(20) unsigned` | No | INDEX | NULL | Afiliación a la que pertenece el detalle. |
| `cc_afiliado` | `bigint(20) unsigned` | No | INDEX | NULL | Afiliado incluido en la afiliación. |
| `id_estado` | `bigint(20) unsigned` | No | INDEX | NULL | Estado del vínculo entre afiliación y afiliado. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `detalle_procedimiento`

**Propósito.** Ítems de ejecución de un procedimiento, con descripción, cantidad y resultado.

**Llaves foráneas.**

- `id_procedimiento` → `procedimiento.id` (`ON DELETE CASCADE`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- `chk_detalle_procedimiento_cantidad`: ``cantidad` > 0`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `id_procedimiento` | `bigint(20) unsigned` | No | INDEX | NULL | Procedimiento al que pertenece el ítem. |
| `descripcion` | `varchar(255)` | No | — | NULL | Descripción del ítem ejecutado. |
| `cantidad` | `int(10) unsigned` | No | — | `1` | Cantidad ejecutada; debe ser mayor que cero. |
| `resultado` | `text` | Sí | — | `NULL` | Resultado observado del ítem. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `diagnostico`

**Propósito.** Diagnósticos clínicos emitidos por un médico a un paciente, vinculados a historia clínica y empresa.

**Llaves foráneas.**

- `cedula_medico` → `medico.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `cedula_paciente` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_historia_clinica` → `historia_clinica.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Empresa o IPS donde se emitió el diagnóstico. |
| `cedula_paciente` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del paciente diagnosticado. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del médico que emite el diagnóstico. |
| `id_historia_clinica` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Historia clínica asociada al diagnóstico. |
| `descripcion` | `varchar(255)` | No | — | NULL | Descripción del diagnóstico. |
| `tipo` | `varchar(50)` | Sí | — | `NULL` | Tipo de diagnóstico, principal o secundario. |
| `fecha` | `date` | No | INDEX | NULL | Fecha en que se emitió el diagnóstico. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `empresas`

**Propósito.** Empresas o IPS clientes con licencia, plan, NIT único y estado contractual.

**Llaves foráneas.**

- `licencia_id` → `licencias.id` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `usuario_cedula` → `usuarios.cedula` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- `chk_empresas_estado`: ``estado` in ('activa','prueba','suspendida','inactiva','vencida')`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `usuario_cedula` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cédula del usuario administrador de la empresa. |
| `licencia_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Licencia contratada por la empresa. |
| `nit` | `varchar(30)` | No | UNIQUE | NULL | NIT de la empresa; valor único en la tabla activa. |
| `nombre` | `varchar(150)` | No | — | NULL | Nombre de la empresa o IPS. |
| `tipo_entidad` | `varchar(20)` | Sí | — | `NULL` | Tipo de entidad de la empresa. |
| `representante_legal` | `varchar(150)` | Sí | — | `NULL` | Representante legal de la empresa. |
| `correo` | `varchar(150)` | Sí | — | `NULL` | Correo de contacto de la empresa. |
| `telefono` | `varchar(20)` | Sí | — | `NULL` | Teléfono de contacto de la empresa. |
| `departamento` | `varchar(80)` | Sí | — | `NULL` | Departamento de la empresa. |
| `ciudad` | `varchar(80)` | Sí | — | `NULL` | Ciudad de la empresa. |
| `direccion` | `varchar(200)` | Sí | — | `NULL` | Dirección de la empresa. |
| `plan` | `varchar(50)` | Sí | — | `NULL` | Plan contratado por la empresa. |
| `fecha_compra` | `date` | Sí | — | `NULL` | Fecha en que se compró la licencia. |
| `fecha_expiracion` | `date` | Sí | — | `NULL` | Fecha de expiración de la licencia. |
| `estado` | `varchar(20)` | No | — | `'activa'` | Estado contractual de la empresa: activa, prueba, suspendida, inactiva o vencida. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `empresas_respaldo_dba11`

**Propósito.** Respaldo de empresas antes de la unificación por NIT realizada en DBA-11.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- `chk_empresas_estado`: ``estado` in ('activa','prueba','suspendida','inactiva','vencida')`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `usuario_cedula` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cédula del usuario relacionado. |
| `licencia_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Licencia relacionada con el registro. |
| `nit` | `varchar(30)` | No | — | NULL | NIT de la entidad. |
| `nombre` | `varchar(150)` | No | — | NULL | Nombre del registro. |
| `tipo_entidad` | `varchar(20)` | Sí | — | `NULL` | Tipo entidad del registro. |
| `representante_legal` | `varchar(150)` | Sí | — | `NULL` | Representante legal del registro. |
| `correo` | `varchar(150)` | Sí | — | `NULL` | Correo electrónico de contacto. |
| `telefono` | `varchar(20)` | Sí | — | `NULL` | Teléfono de contacto. |
| `departamento` | `varchar(80)` | Sí | — | `NULL` | Departamento del registro. |
| `ciudad` | `varchar(80)` | Sí | — | `NULL` | Nombre de la ciudad. |
| `direccion` | `varchar(200)` | Sí | — | `NULL` | Dirección de contacto. |
| `plan` | `varchar(50)` | Sí | — | `NULL` | Plan contratado o asociado. |
| `fecha_compra` | `date` | Sí | — | `NULL` | Fecha de compra. |
| `fecha_expiracion` | `date` | Sí | — | `NULL` | Fecha de expiración. |
| `estado` | `varchar(20)` | No | — | `'activa'` | Estado del registro. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
| `accion_dba11` | `varchar(20)` | No | — | NULL | Acción de la migración DBA-11 que generó esta copia de respaldo. |

## `enfermedad`

**Propósito.** Antecedentes o enfermedades registradas en la historia clínica del paciente por empresa.

**Llaves foráneas.**

- `cedula_medico` → `medico.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `cedula_paciente` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_historia_clinica` → `historia_clinica.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Empresa o IPS donde se registró la enfermedad. |
| `cedula_paciente` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del paciente con el antecedente. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del médico que registró el antecedente. |
| `id_historia_clinica` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Historia clínica asociada al antecedente. |
| `nombre` | `varchar(150)` | No | — | NULL | Nombre de la enfermedad o antecedente. |
| `descripcion` | `text` | Sí | — | `NULL` | Descripción del antecedente clínico. |
| `fecha` | `date` | No | INDEX | NULL | Fecha de registro del antecedente. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `eps`

**Propósito.** Entidades promotoras de salud con NIT, sede y teléfono de contacto.

**Llaves foráneas.**

- `id_sede` → `sedes.id_sede` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `nit_eps` | `varchar(20)` | No | PK | NULL | NIT de la EPS. |
| `nombre_eps` | `varchar(255)` | No | — | NULL | Nombre de la EPS. |
| `telefono_contacto` | `varchar(20)` | No | — | NULL | Teléfono de contacto de la EPS. |
| `id_sede` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Sede asociada a la EPS. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `especialidad`

**Propósito.** Catálogo de especialidades médicas.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `nombre` | `varchar(100)` | No | UNIQUE | NULL | Nombre del registro. |
| `descripcion` | `varchar(255)` | Sí | — | `NULL` | Descripción del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `estado`

**Propósito.** Catálogo genérico de estados usado por afiliación y detalle de afiliación.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_estado` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Estado relacionado con el registro. |
| `estado` | `varchar(50)` | No | — | NULL | Nombre del estado genérico. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `estados_civiles`

**Propósito.** Catálogo de estados civiles de los beneficiarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_estado_civil` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_estado_civil. |
| `nombre` | `varchar(50)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `estado_cita`

**Propósito.** Catálogo de estados de la cita médica.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `nombre` | `varchar(50)` | No | UNIQUE | NULL | Nombre del registro. |
| `descripcion` | `varchar(255)` | Sí | — | `NULL` | Descripción del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `estratos`

**Propósito.** Catálogo de estratos socioeconómicos permitidos, del 1 al 6.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_estrato` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_estrato. |
| `numero` | `int(11)` | No | UNIQUE | NULL | Número de estrato socioeconómico, entre 1 y 6. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `generos`

**Propósito.** Catálogo de géneros de los beneficiarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_genero` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Género relacionado con el registro. |
| `nombre` | `varchar(20)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `grupos_etnicos`

**Propósito.** Catálogo de grupos étnicos de los beneficiarios; dato sensible cifrado en la ficha.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_grupo_etnico` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_grupo_etnico. |
| `nombre` | `varchar(50)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `historia_clinica`

**Propósito.** Historia clínica por empresa, paciente, médico y cita; sus cambios son auditados por triggers.

**Llaves foráneas.**

- `cedula_medico` → `medico.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `cedula_paciente` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_cita` → `cita.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Empresa o IPS a la que pertenece la historia clínica. |
| `cedula_paciente` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del paciente titular de la historia clínica. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del médico que atiende. |
| `id_cita` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cita que originó la historia clínica. |
| `fecha` | `date` | No | INDEX | NULL | Fecha de la atención clínica. |
| `motivo_consulta` | `text` | Sí | — | `NULL` | Motivo por el que el paciente consulta. |
| `antecedentes` | `text` | Sí | — | `NULL` | Antecedentes personales y familiares registrados. |
| `examen_fisico` | `text` | Sí | — | `NULL` | Hallazgos del examen físico. |
| `observaciones` | `text` | Sí | — | `NULL` | Observaciones clínicas adicionales. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si la historia clínica está activa. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `horario_medico`

**Propósito.** Disponibilidad de un médico en un consultorio para una fecha y franja horaria.

**Llaves foráneas.**

- `cedula_medico` → `medico.cedula` (`ON DELETE CASCADE`, `ON UPDATE CASCADE`).
- `id_consultorio` → `consultorio.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- `chk_horario_medico_hora`: ``hora_fin` > `hora_inicio``.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Médico al que pertenece el horario. |
| `id_consultorio` | `bigint(20) unsigned` | No | INDEX | NULL | Consultorio donde se cumple el horario. |
| `fecha` | `date` | No | — | NULL | Fecha del horario de atención. |
| `hora_inicio` | `time` | No | — | NULL | Hora de inicio de la disponibilidad. |
| `hora_fin` | `time` | No | — | NULL | Hora de fin de la disponibilidad; debe ser mayor que hora_inicio. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `licencias`

**Propósito.** Licencias contratadas por una empresa o IPS, con plan, vigencia y estado.

**Llaves foráneas.**

- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `plan_id` → `planes.id` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `usuario_cedula` → `usuarios.cedula` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- `chk_licencias_estado`: ``estado` in ('activa','prueba','suspendida','inactiva','vencida')`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | No | INDEX | NULL | Empresa o IPS dueña de la licencia; apunta a empresas.id. |
| `usuario_cedula` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cédula del usuario que compró la licencia. |
| `nombre_ips` | `varchar(255)` | No | — | NULL | Nombre de la IPS licenciada. |
| `nit` | `varchar(30)` | No | — | NULL | NIT de la IPS licenciada. |
| `tipo_entidad` | `varchar(30)` | Sí | — | `NULL` | Tipo de entidad de la IPS. |
| `representante_legal` | `varchar(150)` | Sí | — | `NULL` | Representante legal de la IPS. |
| `correo_ips` | `varchar(150)` | Sí | — | `NULL` | Correo de contacto de la IPS. |
| `telefono_ips` | `varchar(20)` | Sí | — | `NULL` | Teléfono de contacto de la IPS. |
| `departamento` | `varchar(80)` | Sí | — | `NULL` | Departamento de la IPS. |
| `ciudad` | `varchar(80)` | Sí | — | `NULL` | Ciudad de la IPS. |
| `direccion` | `varchar(200)` | Sí | — | `NULL` | Dirección de la IPS. |
| `contacto_nombre` | `varchar(255)` | Sí | — | `NULL` | Nombre de la persona de contacto comercial. |
| `contacto_email` | `varchar(255)` | Sí | — | `NULL` | Correo de la persona de contacto comercial. |
| `contacto_telefono` | `varchar(20)` | Sí | — | `NULL` | Teléfono de la persona de contacto comercial. |
| `plan` | `varchar(255)` | No | — | NULL | Nombre del plan contratado guardado como texto. |
| `plan_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Plan contratado; apunta a planes.id. |
| `fecha_inicio` | `date` | No | — | NULL | Fecha de inicio de vigencia de la licencia. |
| `fecha_fin` | `date` | No | — | NULL | Fecha de fin de vigencia de la licencia. |
| `estado` | `varchar(255)` | No | — | `'activa'` | Estado de la licencia: activa, prueba, suspendida, inactiva o vencida. |
| `notas` | `text` | Sí | — | `NULL` | Notas observadas durante la venta o renovación. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `log_ingresos`

**Propósito.** Registro de ingresos de usuarios, médicos y cuentas Laravel, alimentado por triggers.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `cedula` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula o identificador del usuario que ingresó. |
| `tipo_usuario` | `varchar(50)` | No | INDEX | NULL | Tipo de usuario que ingresó, tomado del rol o del catálogo de tipos. |
| `fecha` | `date` | No | INDEX | NULL | Fecha del ingreso. |
| `hora` | `time` | No | — | NULL | Hora del ingreso. |

## `log_ingresos_historico`

**Propósito.** Histórico frío de log_ingresos después de la purga configurable.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `cedula` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula de la persona del registro. |
| `tipo_usuario` | `varchar(50)` | No | INDEX | NULL | Tipo usuario del registro. |
| `fecha` | `date` | No | INDEX | NULL | Fecha del hecho registrado. |
| `hora` | `time` | No | — | NULL | Hora del hecho registrado. |

## `log_licencias`

**Propósito.** Registro de cambios de plan en licencias, alimentado por trg_log_cambio_plan.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `licencia_id` | `bigint(20) unsigned` | No | — | NULL | Licencia cuyo plan cambió. |
| `nombre_ips` | `varchar(255)` | Sí | — | `NULL` | Nombre de la IPS al momento del cambio. |
| `plan_anterior` | `varchar(100)` | Sí | — | `NULL` | Plan anterior de la licencia. |
| `plan_nuevo` | `varchar(100)` | Sí | — | `NULL` | Plan nuevo de la licencia. |
| `fecha_cambio` | `datetime` | No | — | `current_timestamp()` | Fecha y hora del cambio de plan. |

## `log_licencias_historico`

**Propósito.** Histórico frío de log_licencias después de la purga configurable.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `int(10) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `licencia_id` | `bigint(20) unsigned` | No | — | NULL | Licencia relacionada con el registro. |
| `nombre_ips` | `varchar(255)` | Sí | — | `NULL` | Nombre de la IPS. |
| `plan_anterior` | `varchar(100)` | Sí | — | `NULL` | Plan anterior del registro. |
| `plan_nuevo` | `varchar(100)` | Sí | — | `NULL` | Plan nuevo del registro. |
| `fecha_cambio` | `datetime` | No | — | `current_timestamp()` | Fecha y hora del cambio registrado. |

## `medicamento`

**Propósito.** Catálogo de medicamentos con código, presentación y stock disponible.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `codigo` | `varchar(50)` | No | PK | NULL | Código único del medicamento en el catálogo. |
| `nombre` | `varchar(150)` | No | UNIQUE | NULL | Nombre del medicamento. |
| `presentacion` | `varchar(100)` | Sí | — | `NULL` | Presentación farmacéutica del medicamento. |
| `descripcion` | `text` | Sí | — | `NULL` | Descripción del medicamento. |
| `stock` | `int(10) unsigned` | No | — | `0` | Unidades disponibles en inventario. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `medico`

**Propósito.** Profesional médico con credenciales, especialidad, contacto y último ingreso.

**Llaves foráneas.**

- `id_especialidad` → `especialidad.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- `chk_medico_estado`: ``estado` in ('activo','inactivo')`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `cedula` | `bigint(20) unsigned` | No | PK | NULL | Cédula del médico; llave natural del profesional. |
| `nombres` | `varchar(100)` | No | — | NULL | Nombres del médico. |
| `apellidos` | `varchar(100)` | No | — | NULL | Apellidos del médico. |
| `foto` | `varchar(255)` | Sí | — | `NULL` | Ruta o nombre de la foto del médico. |
| `correo` | `varchar(150)` | No | UNIQUE | NULL | Correo electrónico del médico. |
| `telefono` | `varchar(20)` | Sí | — | `NULL` | Teléfono del médico. |
| `direccion` | `varchar(200)` | Sí | — | `NULL` | Dirección del médico. |
| `contrasena` | `varchar(255)` | No | — | NULL | Hash de la contraseña del médico. |
| `estado` | `varchar(20)` | No | — | `'activo'` | Estado del médico: activo o inactivo. |
| `id_especialidad` | `bigint(20) unsigned` | No | INDEX | NULL | Especialidad médica del profesional. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
| `ultimo_ingreso` | `datetime` | Sí | — | `NULL` | Fecha y hora del último ingreso del médico. |

## `niveles_educativos`

**Propósito.** Catálogo de niveles educativos de los beneficiarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_nivel_educativo` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_nivel_educativo. |
| `nombre` | `varchar(50)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `ocupaciones`

**Propósito.** Catálogo de ocupaciones de los beneficiarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_ocupacion` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_ocupacion. |
| `nombre` | `varchar(100)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `paises`

**Propósito.** Catálogo de países usado en nacionalidad, nacimiento y expedición de documentos.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_pais` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | País relacionado con el registro. |
| `nombre` | `varchar(100)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `parentescos`

**Propósito.** Catálogo de parentescos entre cotizante y beneficiario.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_parentesco` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Parentesco relacionado con el registro. |
| `nombre` | `varchar(40)` | No | UNIQUE | NULL | Nombre del registro. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `planes`

**Propósito.** Planes comerciales con precios, beneficios, exclusiones y presentación en la web.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- `beneficios`: `json_valid(`beneficios`)`.
- `no_incluye`: `json_valid(`no_incluye`)`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `slug` | `varchar(255)` | No | UNIQUE | NULL | Identificador textual del plan para URLs. |
| `nombre` | `varchar(255)` | No | — | NULL | Nombre comercial del plan. |
| `etiqueta` | `varchar(255)` | Sí | — | `NULL` | Etiqueta corta del plan. |
| `descripcion` | `text` | Sí | — | `NULL` | Descripción comercial del plan. |
| `precio_total` | `bigint(20) unsigned` | Sí | — | `NULL` | Precio total del plan. |
| `periodo` | `varchar(255)` | Sí | — | `NULL` | Periodo de facturación del plan. |
| `ideal` | `text` | Sí | — | `NULL` | Texto que indica para quién es ideal el plan. |
| `beneficios` | `longtext` | Sí | — | `NULL` | JSON con los beneficios incluidos en el plan. |
| `no_incluye` | `longtext` | Sí | — | `NULL` | JSON con los servicios excluidos del plan. |
| `cta` | `varchar(255)` | Sí | — | `NULL` | Texto del botón de llamado a la acción. |
| `cta_url` | `varchar(255)` | Sí | — | `NULL` | URL del botón de llamado a la acción. |
| `icono` | `varchar(255)` | Sí | — | `NULL` | Nombre o ruta del ícono del plan. |
| `clase_icono` | `varchar(255)` | Sí | — | `NULL` | Clase CSS del ícono del plan. |
| `destacada` | `tinyint(1)` | No | — | `0` | Indica si el plan se muestra como destacado. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `orden` | `int(10) unsigned` | No | — | `0` | Orden de presentación del plan. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `prescripcion`

**Propósito.** Prescripciones médicas por empresa y cita, con medicamento, dosis, vigencia y entrega en farmacia.

**Llaves foráneas.**

- `cedula_farmaceuta` → `usuarios.cedula` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `cedula_medico` → `medico.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `cedula_paciente` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_cita` → `cita.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).
- `id_medicamento` → `medicamento.codigo` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- `chk_prescripcion_estado`: ``estado` in ('pendiente','entregada')`.
- `chk_prescripcion_fecha_limite`: ``fecha_limite` is null or `fecha_limite` >= `fecha``.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Empresa o IPS donde se emitió la prescripción. |
| `id_cita` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cita que originó la prescripción. |
| `cedula_paciente` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del paciente al que se prescribe. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del médico que prescribe. |
| `id_medicamento` | `varchar(50)` | Sí | INDEX | `NULL` | Medicamento del catálogo prescrito. |
| `medicamento` | `varchar(150)` | No | — | NULL | Nombre del medicamento copiado en la prescripción. |
| `dosis` | `varchar(100)` | Sí | — | `NULL` | Dosis indicada por el médico. |
| `frecuencia` | `varchar(100)` | Sí | — | `NULL` | Frecuencia con la que debe tomarse el medicamento. |
| `duracion` | `varchar(100)` | Sí | — | `NULL` | Duración del tratamiento. |
| `indicaciones` | `text` | Sí | — | `NULL` | Indicaciones adicionales para el paciente. |
| `fecha` | `date` | No | INDEX | NULL | Fecha en que se emitió la prescripción. |
| `fecha_limite` | `date` | Sí | INDEX | `NULL` | Fecha límite de entrega; no puede ser menor que fecha. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si la prescripción está activa. |
| `estado` | `varchar(20)` | No | INDEX | `'pendiente'` | Estado de la prescripción: pendiente o entregada. |
| `fecha_entrega` | `datetime` | Sí | — | `NULL` | Fecha y hora en que se entregó el medicamento. |
| `cedula_farmaceuta` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cédula del farmaceuta que entregó el medicamento. |
| `cantidad_entregada` | `int(10) unsigned` | Sí | — | `NULL` | Cantidad de medicamento entregada por la farmacia. |
| `observacion_entrega` | `varchar(255)` | Sí | — | `NULL` | Observaciones registradas al entregar la prescripción. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `procedimiento`

**Propósito.** Procedimientos clínicos ejecutados por empresa, cita, médico y paciente.

**Llaves foráneas.**

- `cedula_medico` → `medico.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `cedula_paciente` → `usuarios.cedula` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `empresa_id` → `empresas.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).
- `id_cita` → `cita.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).
- `id_tipo_procedimiento` → `tipo_procedimiento.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `empresa_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Empresa o IPS donde se ejecutó el procedimiento. |
| `cedula_paciente` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del paciente al que se aplicó el procedimiento. |
| `cedula_medico` | `bigint(20) unsigned` | No | INDEX | NULL | Cédula del médico que ejecutó el procedimiento. |
| `id_cita` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cita asociada al procedimiento. |
| `id_tipo_procedimiento` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Tipo de procedimiento ejecutado. |
| `fecha` | `date` | No | INDEX | NULL | Fecha de ejecución del procedimiento. |
| `descripcion` | `varchar(255)` | No | — | NULL | Descripción del procedimiento realizado. |
| `observaciones` | `text` | Sí | — | `NULL` | Observaciones clínicas del procedimiento. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `regimen`

**Propósito.** Catálogo de regímenes de afiliación, usado como tipo de afiliación.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_regimen` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_regimen. |
| `nombre_regimen` | `varchar(255)` | No | — | NULL | Nombre del régimen de afiliación. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `sedes`

**Propósito.** Sedes físicas de la IPS, ubicadas por ciudad.

**Llaves foráneas.**

- `id_ciudad` → `ciudades.id_ciudad` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_sede` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Sede relacionada con el registro. |
| `nombre_sede` | `varchar(255)` | No | — | NULL | Nombre de la sede física. |
| `direccion` | `varchar(255)` | No | — | NULL | Dirección de la sede. |
| `telefono` | `varchar(20)` | No | — | NULL | Teléfono de la sede. |
| `id_ciudad` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Ciudad donde está ubicada la sede. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `sub_tipo_paciente`

**Propósito.** Subtipos de paciente usados para clasificar a los usuarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_sub_tipo_paciente` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_sub_tipo_paciente. |
| `nombre` | `varchar(30)` | No | UNIQUE | NULL | Nombre del subtipo de paciente. |
| `descripcion` | `varchar(150)` | Sí | — | `NULL` | Descripción del subtipo de paciente. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `tipos_sangre`

**Propósito.** Catálogo de tipos de sangre; dato sensible cifrado en la ficha del beneficiario.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_tipo_sangre` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_tipo_sangre. |
| `nombre` | `varchar(10)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `tipos_usuario`

**Propósito.** Catálogo de tipos de usuario del sistema, como paciente, médico o administrador.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `nombre` | `varchar(50)` | No | — | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `tipo_cita`

**Propósito.** Tipos de cita médica con código CUPS, duración y especialidad opcional.

**Llaves foráneas.**

- `id_especialidad` → `especialidad.id` (`ON DELETE SET NULL`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `nombre` | `varchar(191)` | No | UNIQUE | NULL | Nombre del tipo de cita. |
| `codigo_cups` | `varchar(10)` | Sí | UNIQUE | `NULL` | Código CUPS del procedimiento o consulta. |
| `descripcion` | `varchar(255)` | Sí | — | `NULL` | Descripción del tipo de cita. |
| `categoria` | `varchar(80)` | Sí | — | `NULL` | Categoría clínica del tipo de cita. |
| `fuente` | `varchar(60)` | Sí | — | `NULL` | Fuente del catálogo de tipos de cita. |
| `duracion_minutos` | `smallint(5) unsigned` | No | — | `30` | Duración estimada de la cita en minutos. |
| `id_especialidad` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Especialidad médica requerida para el tipo de cita. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `tipo_documento`

**Propósito.** Catálogo de tipos de documento usado por usuarios y beneficiarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_tipo_documento` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Tipo de documento relacionado con el registro. |
| `tipo_documento` | `varchar(100)` | No | — | NULL | Nombre del tipo de documento, por ejemplo cédula o pasaporte. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |

## `tipo_procedimiento`

**Propósito.** Catálogo de tipos de procedimiento clínico.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `nombre` | `varchar(150)` | No | UNIQUE | NULL | Nombre del registro. |
| `descripcion` | `text` | Sí | — | `NULL` | Descripción del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `transacciones`

**Propósito.** Compras y renovaciones de licencias, con plan, monto, método y referencia de pago.

**Llaves foráneas.**

- `licencia_id` → `licencias.id` (`ON DELETE CASCADE`, `ON UPDATE RESTRICT`).
- `plan_id` → `planes.id` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `usuario_cedula` → `usuarios.cedula` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).

**Restricciones CHECK.**

- `chk_transacciones_tipo`: ``tipo` in ('compra','renovacion')`.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador interno autoincremental del registro. |
| `licencia_id` | `bigint(20) unsigned` | No | INDEX | NULL | Licencia asociada a la transacción. |
| `usuario_cedula` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Cédula del usuario que realizó el pago. |
| `plan_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Plan comprado o renovado. |
| `monto` | `bigint(20) unsigned` | No | — | `0` | Monto pagado en la transacción. |
| `tipo` | `varchar(255)` | No | — | `'compra'` | Tipo de transacción: compra o renovación. |
| `metodo_pago` | `varchar(255)` | No | — | `'tarjeta_simulada'` | Método de pago utilizado. |
| `referencia_pago` | `varchar(255)` | Sí | — | `NULL` | Referencia o comprobante del pago. |
| `estado` | `varchar(255)` | No | — | `'aprobado'` | Estado de la transacción. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |

## `users`

**Propósito.** Usuarios de autenticación Laravel con rol y último ingreso.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | No | PK | NULL | Identificador interno de la cuenta Laravel. |
| `name` | `varchar(255)` | No | — | NULL | Nombre de la cuenta de autenticación. |
| `email` | `varchar(255)` | No | UNIQUE | NULL | Correo usado para iniciar sesión. |
| `email_verified_at` | `datetime` | Sí | — | `NULL` | Fecha y hora en que se verificó el correo. |
| `password` | `varchar(255)` | No | — | NULL | Hash de la contraseña Laravel. |
| `rol` | `varchar(30)` | No | — | `'superadmin'` | Rol de la cuenta, usado también en el registro de ingresos. |
| `remember_token` | `varchar(100)` | Sí | — | `NULL` | Token Laravel para mantener la sesión. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
| `ultimo_ingreso` | `datetime` | Sí | — | `NULL` | Fecha y hora del último ingreso de la cuenta. |

## `usuarios`

**Propósito.** Pacientes y usuarios del negocio identificados por cédula, con EPS, ciudad y tipo de usuario.

**Llaves foráneas.**

- `id_ciudad` → `ciudades.id_ciudad` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_sub_tipo_paciente` → `sub_tipo_paciente.id_sub_tipo_paciente` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `id_tipo_documento` → `tipo_documento.id_tipo_documento` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `licencia_id` → `licencias.id` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `nit_eps` → `eps.nit_eps` (`ON DELETE SET NULL`, `ON UPDATE RESTRICT`).
- `tipo_usuario_id` → `tipos_usuario.id` (`ON DELETE RESTRICT`, `ON UPDATE CASCADE`).

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `cedula` | `bigint(20) unsigned` | No | PK | NULL | Cédula del usuario o paciente; llave natural del negocio. |
| `nombres` | `varchar(100)` | No | — | NULL | Nombres del usuario. |
| `apellidos` | `varchar(100)` | No | — | NULL | Apellidos del usuario. |
| `telefono` | `varchar(20)` | Sí | — | `NULL` | Teléfono principal del usuario. |
| `id_ciudad` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Ciudad de residencia del usuario. |
| `id_tipo_documento` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Tipo de documento del usuario. |
| `nit_eps` | `varchar(20)` | Sí | INDEX | `NULL` | EPS del usuario. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el usuario está activo. |
| `correo` | `varchar(150)` | No | UNIQUE | NULL | Correo electrónico del usuario. |
| `contrasena` | `varchar(255)` | No | — | NULL | Hash de la contraseña del usuario. |
| `foto` | `varchar(255)` | Sí | — | `NULL` | Ruta o nombre de la foto del usuario. |
| `codigo` | `varchar(6)` | Sí | — | `NULL` | Código temporal para activación o recuperación de acceso. |
| `codigo_expira_en` | `datetime` | Sí | — | `NULL` | Fecha y hora de expiración del código temporal. |
| `tipo_usuario_id` | `bigint(20) unsigned` | No | INDEX | NULL | Tipo de usuario asignado, usado por el trigger de ingresos. |
| `id_sub_tipo_paciente` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Subtipo de paciente asignado al usuario. |
| `licencia_id` | `bigint(20) unsigned` | Sí | INDEX | `NULL` | Licencia corporativa a la que pertenece el usuario. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
| `debe_crear_contrasena` | `tinyint(1)` | No | — | `0` | Indica que el usuario debe crear su contraseña antes de usar el sistema. |
| `ultimo_ingreso` | `datetime` | Sí | — | `NULL` | Fecha y hora del último ingreso del usuario. |

## `zonas_residencia`

**Propósito.** Catálogo de zonas de residencia de los beneficiarios.

**Llaves foráneas.**

- Ninguna.

**Restricciones CHECK.**

- Ninguna.

| Columna | Tipo | Nulo | Llave | Default | Descripción |
|---|---|---|---|---|---|
| `id_zona_residencia` | `bigint(20) unsigned` | No | PK, auto_increment | NULL | Identificador de la entidad referenciada por id_zona_residencia. |
| `nombre` | `varchar(20)` | No | UNIQUE | NULL | Nombre del registro. |
| `activo` | `tinyint(1)` | No | — | `1` | Indica si el registro está activo para uso. |
| `created_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de creación del registro. |
| `updated_at` | `datetime` | Sí | — | `NULL` | Fecha y hora de la última actualización del registro. |
