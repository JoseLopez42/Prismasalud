<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\TipoUsuario;
use App\Models\Admin\Ciudad;
use App\Models\Admin\TipoDocumento;
use App\Models\Admin\Beneficiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    const TIPO_ADMINISTRADOR = 1;

    // Lista todos los usuarios
    public function index()
    {
        $usuarios = Usuario::with('tipoUsuario')->get();
        $seccionActiva = 'usuarios';
        return view('admin.usuarios.index', compact('usuarios', 'seccionActiva'));
    }

    // Formulario para crear un nuevo usuario
    public function create()
    {
        $tiposUsuario = TipoUsuario::all();
        $ciudades = Ciudad::all();
        $tiposDocumento = TipoDocumento::all();

        // Catálogos para los campos adicionales del paciente
        $paises = ['Colombia' => 'Colombia', 'Venezuela' => 'Venezuela', 'Estados Unidos' => 'Estados Unidos', 'España' => 'España', 'México' => 'México', 'Argentina' => 'Argentina', 'Chile' => 'Chile', 'Perú' => 'Perú', 'Ecuador' => 'Ecuador', 'Otro' => 'Otro'];
        $generos = ['Masculino' => 'Masculino', 'Femenino' => 'Femenino', 'Otro' => 'Otro'];
        $estadosCiviles = ['Soltero' => 'Soltero', 'Casado' => 'Casado', 'Unión libre' => 'Unión libre', 'Divorciado' => 'Divorciado', 'Viudo' => 'Viudo'];
        $gruposEtnicos = ['Ninguno' => 'Ninguno', 'Indígena' => 'Indígena', 'Afrodescendiente' => 'Afrodescendiente', 'Raizal' => 'Raizal', 'Palenquero' => 'Palenquero', 'Gitano' => 'Gitano', 'Otro' => 'Otro'];
        $zonasResidencia = ['Urbana' => 'Urbana', 'Rural' => 'Rural'];
        $estratos = range(1, 6);
        $nivelesEducativos = ['Ninguno' => 'Ninguno', 'Primaria' => 'Primaria', 'Secundaria' => 'Secundaria', 'Técnica' => 'Técnica', 'Tecnológica' => 'Tecnológica', 'Universitaria' => 'Universitaria', 'Posgrado' => 'Posgrado'];
        $tiposSangre = ['O+' => 'O+', 'O-' => 'O-', 'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-'];
        $ocupaciones = [
            'Agricultor' => 'Agricultor', 'Comerciante' => 'Comerciante', 'Conductor' => 'Conductor', 'Contador' => 'Contador',
            'Ingeniero' => 'Ingeniero', 'Médico' => 'Médico', 'Enfermero' => 'Enfermero', 'Profesor' => 'Profesor',
            'Abogado' => 'Abogado', 'Técnico' => 'Técnico', 'Obrero' => 'Obrero', 'Empleado' => 'Empleado',
            'Estudiante' => 'Estudiante', 'Ama de casa' => 'Ama de casa', 'Pensionado' => 'Pensionado', 'Desempleado' => 'Desempleado', 'Otro' => 'Otro',
        ];

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
        $datos = $request->validate([
            'cedula'            => 'required|integer|unique:usuarios,cedula',
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'edad'              => 'nullable|integer|min:0|max:120',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'required|email|unique:usuarios,correo',
            'contrasena'        => 'required|string|min:6',
            'tipo_usuario_id'   => 'required|exists:tipos_usuario,id',
            'id_ciudad'         => 'nullable|exists:ciudades,id_ciudad',
            'id_tipo_documento' => 'nullable|exists:tipo_documento,id_tipo_documento',
            'foto'              => 'nullable|image|max:2048', // máx 2MB
        ]);

        // Si el rol es Paciente (id=2), validamos también los campos adicionales de paciente
        $tipoUsuario = TipoUsuario::find($request->tipo_usuario_id);
        $esPaciente = $tipoUsuario && strtolower($tipoUsuario->nombre) === 'paciente';

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
            if ($esPaciente && $datosPaciente) {
                $ocupacionFinal = $datosPaciente['ocupacion'] === 'Otro'
                    ? ($datosPaciente['ocupacion_otra'] ?? null)
                    : $datosPaciente['ocupacion'];

                Beneficiario::create([
                    'cc_afiliado'      => $datos['cedula'],
                    'tipo_persona'     => 'cotizante',
                    'cc_cotizante'     => null,
                    'parentesco'       => null,
                    'nit_eps'          => null,
                    'nombres'          => $datos['nombres'],
                    'apellidos'        => $datos['apellidos'],
                    'edad'             => $datos['edad'],
                    'telefono'         => $datos['telefono'],
                    'direccion'        => '',
                    'correo'           => $datos['correo'],
                    'fecha_nacimiento' => null,

                    // Campos adicionales del paciente (requieren migración en tabla beneficiarios)
                    'id_tipo_documento'   => $datosPaciente['id_tipo_documento'],
                    'pais_expedicion'     => $datosPaciente['pais_expedicion'],
                    'genero'              => $datosPaciente['genero'],
                    'estado_civil'        => $datosPaciente['estado_civil'],
                    'grupo_etnico'        => $datosPaciente['grupo_etnico'],
                    'tiene_discapacidad'  => $request->boolean('tiene_discapacidad'),
                    'tipo_discapacidad'   => $datosPaciente['tipo_discapacidad'],
                    'nacionalidad'        => $datosPaciente['nacionalidad'],
                    'pais_nacimiento'     => $datosPaciente['pais_nacimiento'],
                    'zona_residencia'     => $datosPaciente['zona_residencia'],
                    'telefono_fijo'       => $datosPaciente['telefono_fijo'],
                    'estrato'             => $datosPaciente['estrato'],
                    'nivel_educativo'     => $datosPaciente['nivel_educativo'],
                    'ocupacion'           => $ocupacionFinal,
                    'tipo_sangre'         => $datosPaciente['tipo_sangre'],
                    'victima_conflicto_armado' => $request->boolean('victima_conflicto_armado'),
                    'numero_afiliacion_anterior' => $datosPaciente['numero_afiliacion_anterior'],
                ]);
            }
        });

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.' . ($esPaciente ? ' Se creó también su registro como paciente.' : ''));
    }

    // Formulario para editar un usuario existente
    public function edit($cedula)
    {
        $usuario = Usuario::findOrFail($cedula);
        $tiposUsuario = TipoUsuario::all();
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

        $datos = $request->validate([
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'edad'              => 'nullable|integer|min:0|max:120',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'required|email|unique:usuarios,correo,' . $cedula . ',cedula',
            'contrasena'        => 'nullable|string|min:6',
            'tipo_usuario_id'   => 'required|exists:tipos_usuario,id',
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
