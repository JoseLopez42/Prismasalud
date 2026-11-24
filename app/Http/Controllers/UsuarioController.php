<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\SubTipoPaciente;
use App\Models\Admin\Ciudad;
use App\Models\Admin\TipoDocumento;
use App\Models\Admin\Beneficiario;
use App\Models\Admin\Pais;
use App\Models\Admin\Genero;
use App\Models\Admin\EstadoCivil;
use App\Models\Admin\GrupoEtnico;
use App\Models\Admin\ZonaResidencia;
use App\Models\Admin\Estrato;
use App\Models\Admin\NivelEducativo;
use App\Models\Admin\Ocupacion;
use App\Models\Admin\TipoSangre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    const TIPO_ADMINISTRADOR = 1;

    // Lista todos los usuarios
    public function index()
    {
        $usuarios = Usuario::with(['tipoUsuario', 'subTipoPaciente'])->get();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.index', compact('usuarios', 'seccionActiva'));
    }

    // Formulario para crear un nuevo usuario
    public function create()
    {
        // Solo se muestran los roles administrables (Administrador y Paciente)
        $tiposUsuario = TipoUsuario::asignables()->orderBy('id')->get();
        $ciudades = Ciudad::all();
        $tiposDocumento = TipoDocumento::all();

        // Catálogos para los campos adicionales del paciente (extraídos de la base de datos)
        $paises = Pais::activo()->pluck('nombre', 'nombre');
        $generos = Genero::activo()->pluck('nombre', 'nombre');
        $estadosCiviles = EstadoCivil::activo()->pluck('nombre', 'nombre');
        $gruposEtnicos = GrupoEtnico::activo()->pluck('nombre', 'nombre');
        $zonasResidencia = ZonaResidencia::activo()->pluck('nombre', 'nombre');
        $estratos = Estrato::activo()->pluck('numero', 'numero');
        $nivelesEducativos = NivelEducativo::activo()->pluck('nombre', 'nombre');
        $tiposSangre = TipoSangre::activo()->pluck('nombre', 'nombre');
        $ocupaciones = Ocupacion::activo()->pluck('nombre', 'nombre');

        $seccionActiva = 'usuarios';
        return view('admin.usuarios.create', compact(
            'tiposUsuario', 'ciudades', 'tiposDocumento',
            'paises', 'generos', 'estadosCiviles', 'gruposEtnicos', 'zonasResidencia',
            'estratos', 'nivelesEducativos', 'tiposSangre', 'ocupaciones', 'seccionActiva'
        ));
    }

    // Guarda el nuevo usuario
    public function store(Request $request)
    {
        // Roles permitidos en este formulario (Administrador y Paciente)
        $rolesPermitidos = TipoUsuario::asignables()->pluck('id')->all();

        $datos = $request->validate([
            'cedula'            => 'required|integer|unique:usuarios,cedula',
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'edad'              => 'nullable|integer|min:0|max:120',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'required|email|unique:usuarios,correo',
            'contrasena'        => 'required|string|min:6',
            'tipo_usuario_id'   => ['required', 'exists:tipos_usuario,id', Rule::in($rolesPermitidos)],
            'id_ciudad'         => 'nullable|exists:ciudades,id_ciudad',
            'id_tipo_documento' => 'nullable|exists:tipo_documento,id_tipo_documento',
            'foto'              => 'nullable|image|max:2048', // máx 2MB
        ]);

        // Si el rol es Paciente (id=2), validamos también los campos adicionales de paciente
        $tipoUsuario = TipoUsuario::find($request->tipo_usuario_id);
        $esPaciente = $tipoUsuario && strtolower($tipoUsuario->nombre) === 'paciente';

        // Los pacientes que se crean desde el módulo de Usuarios son COTIZANTES
        // (titulares de la afiliación). Los BENEFICIARIOS se crean desde el
        // módulo de Afiliación.
        $datos['id_sub_tipo_paciente'] = $esPaciente
            ? SubTipoPaciente::idPorNombre('Cotizante')
            : null;

        $datosPaciente = null;
        if ($esPaciente) {
            $datosPaciente = $request->validate([
                'id_tipo_documento'   => ['nullable', 'exists:tipo_documento,id_tipo_documento'],
                'pais_expedicion'     => ['nullable', 'string', 'max:100'],
                'genero'              => ['nullable', 'string', 'max:20', 'in:Masculino,Femenino,Otro'],
                'estado_civil'        => ['nullable', 'string', 'max:50'],
                'grupo_etnico'        => ['nullable', 'string', 'max:50'],
                'tiene_discapacidad'  => ['nullable', 'boolean'],
                'tipo_discapacidad'   => ['nullable', 'string', 'max:100', 'required_if:tiene_discapacidad,1'],
                'nacionalidad'        => ['nullable', 'string', 'max:100'],
                'pais_nacimiento'     => ['nullable', 'string', 'max:100'],
                'zona_residencia'     => ['nullable', 'string', 'max:20', 'in:Urbana,Rural'],
                'telefono_fijo'       => ['nullable', 'string', 'max:20'],
                'estrato'             => ['nullable', 'integer', 'min:1', 'max:6'],
                'nivel_educativo'     => ['nullable', 'string', 'max:50'],
                'ocupacion'           => ['nullable', 'string', 'max:100'],
                'ocupacion_otra'      => ['nullable', 'string', 'max:100', 'required_if:ocupacion,Otro'],
                'tipo_sangre'         => ['nullable', 'string', 'max:5', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-'],
                'victima_conflicto_armado' => ['nullable', 'boolean'],
                'numero_afiliacion_anterior' => ['nullable', 'string', 'max:50'],
            ], [
                'tipo_discapacidad.required_if' => 'Si tiene discapacidad, indica el tipo.',
                'ocupacion_otra.required_if' => 'Si seleccionó "Otro" en ocupación, escriba la ocupación.',
            ]);
        }

        // Si subieron una foto, la guardamos en storage/app/public/usuarios
        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('usuarios', 'public');
        }

        $datos['contrasena'] = bcrypt($datos['contrasena']);

        DB::transaction(function () use ($datos, $request, $esPaciente, &$datosPaciente) {
            $usuario = Usuario::create($datos);

            // Si es Paciente, creamos su beneficiario (como cotizante titular)
            // con los datos adicionales del paciente. NO se crea afiliación.
            if ($esPaciente) {
                $ocupacion = $datosPaciente['ocupacion'] ?? null;
                $ocupacionFinal = $ocupacion === 'Otro'
                    ? ($datosPaciente['ocupacion_otra'] ?? null)
                    : $ocupacion;

                Beneficiario::create([
                    'cc_afiliado'      => $datos['cedula'],
                    'tipo_persona'     => 'cotizante',
                    'cc_cotizante'     => null,
                    'parentesco'       => null,
                    'nit_eps'          => null,
                    'nombres'          => $datos['nombres'],
                    'apellidos'        => $datos['apellidos'],
                    'edad'             => $datos['edad'] ?? null,
                    'telefono'         => $datos['telefono'] ?? null,
                    'direccion'        => $datos['direccion'] ?? '',
                    'correo'           => $datos['correo'],
                    'fecha_nacimiento' => null,

                    // Campos adicionales del paciente (requieren migración en tabla beneficiarios).
                    // Se usa "?? null" porque el formulario deshabilita (y por tanto no
                    // envía) algunos campos, como el tipo de discapacidad.
                    'id_tipo_documento'   => $datos['id_tipo_documento'] ?? null,
                    'pais_expedicion'     => $datosPaciente['pais_expedicion'] ?? null,
                    'genero'              => $datosPaciente['genero'] ?? null,
                    'estado_civil'        => $datosPaciente['estado_civil'] ?? null,
                    'grupo_etnico'        => $datosPaciente['grupo_etnico'] ?? null,
                    'tiene_discapacidad'  => $request->boolean('tiene_discapacidad'),
                    'tipo_discapacidad'   => $datosPaciente['tipo_discapacidad'] ?? null,
                    'nacionalidad'        => $datosPaciente['nacionalidad'] ?? null,
                    'pais_nacimiento'     => $datosPaciente['pais_nacimiento'] ?? null,
                    'zona_residencia'     => $datosPaciente['zona_residencia'] ?? null,
                    'telefono_fijo'       => $datosPaciente['telefono_fijo'] ?? null,
                    'estrato'             => $datosPaciente['estrato'] ?? null,
                    'nivel_educativo'     => $datosPaciente['nivel_educativo'] ?? null,
                    'ocupacion'           => $ocupacionFinal,
                    'tipo_sangre'         => $datosPaciente['tipo_sangre'] ?? null,
                    'victima_conflicto_armado' => $request->boolean('victima_conflicto_armado'),
                    'numero_afiliacion_anterior' => $datosPaciente['numero_afiliacion_anterior'] ?? null,
                ]);
            }
        });

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.' . ($esPaciente ? ' Se creó también su registro como paciente.' : ''));
    }

    // Formulario para editar un usuario existente
    public function edit($cedula)
    {
        $usuario = Usuario::findOrFail($cedula);
        // Solo se muestran los roles administrables (Administrador y Paciente)
        $tiposUsuario = TipoUsuario::asignables()->orderBy('id')->get();
        $ciudades = Ciudad::all();
        $tiposDocumento = TipoDocumento::all();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.edit', compact('usuario', 'tiposUsuario', 'ciudades', 'tiposDocumento', 'seccionActiva'));
    }

    // Actualiza el usuario. La contraseña y la foto son opcionales:
    // si llegan vacías, se dejan las que ya tenía.
    public function update(Request $request, $cedula)
    {
        $usuario = Usuario::findOrFail($cedula);

        // Roles permitidos en este formulario (Administrador y Paciente)
        $rolesPermitidos = TipoUsuario::asignables()->pluck('id')->all();

        $datos = $request->validate([
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'edad'              => 'nullable|integer|min:0|max:120',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'required|email|unique:usuarios,correo,' . $cedula . ',cedula',
            'contrasena'        => 'nullable|string|min:6',
            'tipo_usuario_id'   => ['required', 'exists:tipos_usuario,id', Rule::in($rolesPermitidos)],
            'id_ciudad'         => 'nullable|exists:ciudades,id_ciudad',
            'id_tipo_documento' => 'nullable|exists:tipo_documento,id_tipo_documento',
            'foto'              => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('usuarios', 'public');
        }

        if (!empty($datos['contrasena'])) {
            $datos['contrasena'] = bcrypt($datos['contrasena']);
        } else {
            unset($datos['contrasena']);
        }

        // El sub tipo de paciente solo aplica a los pacientes. Si el rol
        // cambia a Paciente y no tenía sub tipo, se asume Cotizante.
        $tipoUsuario = TipoUsuario::find($datos['tipo_usuario_id']);
        $esPaciente = $tipoUsuario && strtolower($tipoUsuario->nombre) === 'paciente';

        if (! $esPaciente) {
            $datos['id_sub_tipo_paciente'] = null;
        } elseif (! $usuario->id_sub_tipo_paciente) {
            $datos['id_sub_tipo_paciente'] = SubTipoPaciente::idPorNombre('Cotizante');
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado correctamente.');
    }

    // Activa o desactiva el usuario (en vez de eliminarlo)
    public function cambiarEstado($cedula)
    {
        $usuario = Usuario::findOrFail($cedula);
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $mensaje = $usuario->activo ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.';

        return redirect()->route('usuarios.index')->with('exito', $mensaje);
    }
}
