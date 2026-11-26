<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->comentarios() as $tabla => $comentario) {
            DB::statement('ALTER TABLE `' . $tabla . '` COMMENT = \'' . addslashes($comentario) . '\'');
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->comentarios()) as $tabla) {
            DB::statement('ALTER TABLE `' . $tabla . '` COMMENT = \'\'');
        }
    }

    private function comentarios(): array
    {
        return array (
  'afiliacion' => 'Registra la afiliación de un usuario o beneficiario a una EPS, régimen, empresa y estado, con su fecha.',
  'auditoria_clinica' => 'Registro inmutable de cambios y bajas de historia clínica para trazabilidad sanitaria (Ley 2015 de 2020).',
  'beneficiarios' => 'Ficha demográfica y de seguridad social de cotizantes y beneficiarios, con datos sensibles cifrados.',
  'catalogo_procedimiento' => 'Catálogo de procedimientos agrupados por tipo, usado al registrar procedimientos clínicos.',
  'cita' => 'Agenda de citas médicas por empresa, paciente, médico, consultorio, tipo y estado, con su franja horaria.',
  'ciudades' => 'Catálogo de ciudades asociadas a un departamento.',
  'consultorio' => 'Consultorios físicos de una sede donde se atienden citas y se programan horarios médicos.',
  'departamentos' => 'Catálogo de departamentos del país.',
  'detalle_afiliado_afiliacion' => 'Relación entre afiliaciones y afiliados, con el estado de cada vínculo.',
  'detalle_procedimiento' => 'Ítems de ejecución de un procedimiento, con descripción, cantidad y resultado.',
  'diagnostico' => 'Diagnósticos clínicos emitidos por un médico a un paciente, vinculados a historia clínica y empresa.',
  'empresas' => 'Empresas o IPS clientes con licencia, plan, NIT único y estado contractual.',
  'empresas_respaldo_dba11' => 'Respaldo de empresas antes de la unificación por NIT realizada en DBA-11.',
  'enfermedad' => 'Antecedentes o enfermedades registradas en la historia clínica del paciente por empresa.',
  'eps' => 'Entidades promotoras de salud con NIT, sede y teléfono de contacto.',
  'especialidad' => 'Catálogo de especialidades médicas.',
  'estado' => 'Catálogo genérico de estados usado por afiliación y detalle de afiliación.',
  'estados_civiles' => 'Catálogo de estados civiles de los beneficiarios.',
  'estado_cita' => 'Catálogo de estados de la cita médica.',
  'estratos' => 'Catálogo de estratos socioeconómicos permitidos, del 1 al 6.',
  'generos' => 'Catálogo de géneros de los beneficiarios.',
  'grupos_etnicos' => 'Catálogo de grupos étnicos de los beneficiarios; dato sensible cifrado en la ficha.',
  'historia_clinica' => 'Historia clínica por empresa, paciente, médico y cita; sus cambios son auditados por triggers.',
  'horario_medico' => 'Disponibilidad de un médico en un consultorio para una fecha y franja horaria.',
  'licencias' => 'Licencias contratadas por una empresa o IPS, con plan, vigencia y estado.',
  'log_ingresos' => 'Registro de ingresos de usuarios, médicos y cuentas Laravel, alimentado por triggers.',
  'log_ingresos_historico' => 'Histórico frío de log_ingresos después de la purga configurable.',
  'log_licencias' => 'Registro de cambios de plan en licencias, alimentado por trg_log_cambio_plan.',
  'log_licencias_historico' => 'Histórico frío de log_licencias después de la purga configurable.',
  'medicamento' => 'Catálogo de medicamentos con código, presentación y stock disponible.',
  'medico' => 'Profesional médico con credenciales, especialidad, contacto y último ingreso.',
  'niveles_educativos' => 'Catálogo de niveles educativos de los beneficiarios.',
  'ocupaciones' => 'Catálogo de ocupaciones de los beneficiarios.',
  'paises' => 'Catálogo de países usado en nacionalidad, nacimiento y expedición de documentos.',
  'parentescos' => 'Catálogo de parentescos entre cotizante y beneficiario.',
  'planes' => 'Planes comerciales con precios, beneficios, exclusiones y presentación en la web.',
  'prescripcion' => 'Prescripciones médicas por empresa y cita, con medicamento, dosis, vigencia y entrega en farmacia.',
  'procedimiento' => 'Procedimientos clínicos ejecutados por empresa, cita, médico y paciente.',
  'regimen' => 'Catálogo de regímenes de afiliación, usado como tipo de afiliación.',
  'sedes' => 'Sedes físicas de la IPS, ubicadas por ciudad.',
  'sub_tipo_paciente' => 'Subtipos de paciente usados para clasificar a los usuarios.',
  'tipos_sangre' => 'Catálogo de tipos de sangre; dato sensible cifrado en la ficha del beneficiario.',
  'tipos_usuario' => 'Catálogo de tipos de usuario del sistema, como paciente, médico o administrador.',
  'tipo_cita' => 'Tipos de cita médica con código CUPS, duración y especialidad opcional.',
  'tipo_documento' => 'Catálogo de tipos de documento usado por usuarios y beneficiarios.',
  'tipo_procedimiento' => 'Catálogo de tipos de procedimiento clínico.',
  'transacciones' => 'Compras y renovaciones de licencias, con plan, monto, método y referencia de pago.',
  'users' => 'Usuarios de autenticación Laravel con rol y último ingreso.',
  'usuarios' => 'Pacientes y usuarios del negocio identificados por cédula, con EPS, ciudad y tipo de usuario.',
  'zonas_residencia' => 'Catálogo de zonas de residencia de los beneficiarios.',
);
    }
};
