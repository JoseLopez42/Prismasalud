<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Afiliacion;
use App\Models\Admin\Beneficiario;
use App\Models\Admin\Estado;
use App\Models\Admin\Parentesco;
use App\Models\Admin\TipoAfiliacion;
use App\Models\Admin\TipoDocumento;
use App\Models\Admin\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AfiliacionController extends Controller
{
    public function index()
    {
        $afiliaciones = Afiliacion::with(['beneficiario', 'eps', 'usuario', 'estado', 'tipoAfiliacion'])->get();
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.afiliacion.index', compact('afiliaciones', 'seccionActiva'));
    }

    public function create()
    {
        $beneficiarios = Beneficiario::with(['cotizante', 'eps'])->get();
        $cotizantes = Beneficiario::with('eps')
            ->where('tipo_persona', 'cotizante')
            ->orderBy('nombres')
            ->get();
        $estados = Estado::all();
        $usuarios = $this->cotizantesComoUsuarios();
        $tipos = TipoAfiliacion::all();
        $tiposDocumento = TipoDocumento::orderBy('tipo_documento')->get();
        $parentescos = Parentesco::orderBy('id_parentesco')->get();
        $seccionActiva = 'afiliaciones';

        // Catálogos estáticos para los campos adicionales de afiliación
        $generos = ['Masculino' => 'Masculino', 'Femenino' => 'Femenino', 'Otro' => 'Otro'];
        $estadosCiviles = ['Soltero' => 'Soltero', 'Casado' => 'Casado', 'Unión libre' => 'Unión libre', 'Divorciado' => 'Divorciado', 'Viudo' => 'Viudo'];
        $gruposEtnicos = ['Ninguno' => 'Ninguno', 'Indígena' => 'Indígena', 'Afrodescendiente' => 'Afrodescendiente', 'Raizal' => 'Raizal', 'Palenquero' => 'Palenquero', 'Gitano' => 'Gitano', 'Otro' => 'Otro'];
        $zonasResidencia = ['Urbana' => 'Urbana', 'Rural' => 'Rural'];
        $estratos = range(1, 6);
        $nivelesEducativos = ['Ninguno' => 'Ninguno', 'Primaria' => 'Primaria', 'Secundaria' => 'Secundaria', 'Técnica' => 'Técnica', 'Tecnológica' => 'Tecnológica', 'Universitaria' => 'Universitaria', 'Posgrado' => 'Posgrado'];
        $tiposSangre = ['O+' => 'O+', 'O-' => 'O-', 'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-'];
        $paises = ['Colombia' => 'Colombia', 'Venezuela' => 'Venezuela', 'Estados Unidos' => 'Estados Unidos', 'España' => 'España', 'México' => 'México', 'Argentina' => 'Argentina', 'Chile' => 'Chile', 'Perú' => 'Perú', 'Ecuador' => 'Ecuador', 'Otro' => 'Otro'];
        $ocupaciones = [
            'Agricultor' => 'Agricultor',
            'Comerciante' => 'Comerciante',
            'Conductor' => 'Conductor',
            'Contador' => 'Contador',
            'Ingeniero' => 'Ingeniero',
            'Médico' => 'Médico',
            'Enfermero' => 'Enfermero',
            'Profesor' => 'Profesor',
            'Abogado' => 'Abogado',
            'Técnico' => 'Técnico',
            'Obrero' => 'Obrero',
            'Empleado' => 'Empleado',
            'Estudiante' => 'Estudiante',
            'Ama de casa' => 'Ama de casa',
            'Pensionado' => 'Pensionado',
            'Desempleado' => 'Desempleado',
            'Otro' => 'Otro',
        ];

        return view('admin.afiliaciones.afiliacion.create', compact(
            'beneficiarios', 'cotizantes', 'estados', 'usuarios', 'tipos', 'tiposDocumento',
            'parentescos', 'generos', 'estadosCiviles', 'gruposEtnicos', 'zonasResidencia',
            'estratos', 'nivelesEducativos', 'tiposSangre', 'paises', 'ocupaciones', 'seccionActiva'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_afiliacion'   => 'required|date',
            'id_tipo_afiliacion' => 'required|exists:regimen,id_regimen',
            'cc_usuario'         => 'required|exists:usuarios,cedula',
            'id_estado'          => 'required|exists:estado,id_estado',
            'origen_beneficiario'=> 'required|in:existente,nuevo',

            // Campos adicionales de afiliación (datos del afiliado en este registro)
            'id_tipo_documento'  => ['nullable', 'exists:tipo_documento,id_tipo_documento'],
            'pais_expedicion'    => ['nullable', 'string', 'max:100'],
            'genero'             => ['nullable', 'string', 'max:20', 'in:Masculino,Femenino,Otro'],
            'estado_civil'       => ['nullable', 'string', 'max:50'],
            'grupo_etnico'       => ['nullable', 'string', 'max:50'],
            'tiene_discapacidad' => ['nullable', 'boolean'],
            'tipo_discapacidad'  => ['nullable', 'string', 'max:100', 'required_if:tiene_discapacidad,1'],
            'nacionalidad'       => ['nullable', 'string', 'max:100'],
            'pais_nacimiento'    => ['nullable', 'string', 'max:100'],
            'zona_residencia'    => ['nullable', 'string', 'max:20', 'in:Urbana,Rural'],
            'telefono_fijo'      => ['nullable', 'string', 'max:20'],
            'estrato'            => ['nullable', 'integer', 'min:1', 'max:6'],
            'nivel_educativo'    => ['nullable', 'string', 'max:50'],
            'ocupacion'          => ['nullable', 'string', 'max:100'],
            'ocupacion_otra'     => ['nullable', 'string', 'max:100', 'required_if:ocupacion,Otro'],
            'tipo_sangre'        => ['nullable', 'string', 'max:5', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-'],
            'victima_conflicto_armado' => ['nullable', 'boolean'],
            'numero_afiliacion_anterior' => ['nullable', 'string', 'max:50'],
        ], [
            'fecha_afiliacion.required'   => 'La fecha de afiliación es obligatoria.',
            'fecha_afiliacion.date'       => 'La fecha de afiliación no es válida.',
            'id_tipo_afiliacion.required' => 'Selecciona el tipo de afiliación.',
            'cc_usuario.required'         => 'Selecciona el cotizante titular.',
            'id_estado.required'          => 'Selecciona el estado.',
            'id_tipo_documento.exists'    => 'El tipo de documento no es válido.',
            'genero.in'                   => 'El género no es válido.',
            'zona_residencia.in'          => 'La zona de residencia no es válida.',
            'estrato.integer'             => 'El estrato debe ser un número del 1 al 6.',
            'estrato.min'                 => 'El estrato debe ser entre 1 y 6.',
            'estrato.max'                 => 'El estrato debe ser entre 1 y 6.',
            'tipo_sangre.in'              => 'El tipo de sangre no es válido.',
            'tipo_discapacidad.required_if' => 'Si tiene discapacidad, indica el tipo.',
            'ocupacion_otra.required_if'    => 'Si seleccionó "Otro" en ocupación, escriba la ocupación.',
        ]);

        // Ruta "beneficiario ya registrado"
        if ($request->origen_beneficiario === 'existente') {
            $request->validate([
                'cc_afiliado' => 'required|exists:beneficiarios,cc_afiliado',
            ]);
        } else {
            // Ruta "nuevo beneficiario": se crea desde aquí, dentro de la afiliación.
            $request->validate([
                'nuevo_cc_cotizante'     => ['required', 'integer', 'exists:beneficiarios,cc_afiliado,tipo_persona,cotizante'],
                'nuevo_parentesco'       => ['required', 'string', 'max:40', 'exists:parentescos,nombre'],
                'nuevo_cc_afiliado'      => ['required', 'integer', 'digits_between:4,12', 'unique:beneficiarios,cc_afiliado', 'unique:usuarios,cedula'],
                'nuevo_nombres'          => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
                'nuevo_apellidos'        => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
                'nuevo_edad'             => ['required', 'integer', 'min:0', 'max:120'],
                'nuevo_telefono'         => ['required', 'string', 'max:20', 'regex:/^[+()\d\s\-]{7,20}$/'],
                'nuevo_direccion'        => ['required', 'string', 'max:255'],
                'nuevo_correo'           => ['required', 'email', 'max:100', 'unique:beneficiarios,correo', 'unique:usuarios,correo'],
                'nuevo_fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
                'nuevo_foto'             => ['nullable', 'image', 'max:2048'],
            ], [
                'nuevo_cc_cotizante.required'     => 'Selecciona de qué cotizante es familiar el nuevo beneficiario.',
                'nuevo_cc_cotizante.exists'        => 'El cotizante seleccionado no existe.',
                'nuevo_parentesco.required'       => 'Selecciona el parentesco del nuevo beneficiario.',
                'nuevo_parentesco.exists'         => 'Selecciona un parentesco de la lista.',
                'nuevo_cc_afiliado.required'      => 'La cédula del nuevo beneficiario es obligatoria.',
                'nuevo_cc_afiliado.unique'        => 'Esa cédula ya existe (como beneficiario o como usuario del sistema).',
                'nuevo_nombres.required'          => 'Los nombres del nuevo beneficiario son obligatorios.',
                'nuevo_apellidos.required'        => 'Los apellidos del nuevo beneficiario son obligatorios.',
                'nuevo_edad.required'             => 'La edad del nuevo beneficiario es obligatoria.',
                'nuevo_telefono.required'         => 'El teléfono del nuevo beneficiario es obligatorio.',
                'nuevo_direccion.required'        => 'La dirección del nuevo beneficiario es obligatoria.',
                'nuevo_correo.required'           => 'El correo del nuevo beneficiario es obligatorio.',
                'nuevo_correo.unique'             => 'Ese correo ya está registrado.',
                'nuevo_fecha_nacimiento.required' => 'La fecha de nacimiento del nuevo beneficiario es obligatoria.',
                'nuevo_foto.image'                => 'La foto debe ser una imagen.',
                'nuevo_foto.max'                  => 'La foto no puede pesar más de 2 MB.',
            ]);

            $edadReal = Carbon::parse($request->nuevo_fecha_nacimiento)->age;
            if (abs((int) $request->nuevo_edad - $edadReal) > 1) {
                return back()->withErrors([
                    'nuevo_edad' => 'La edad no coincide con la fecha de nacimiento del nuevo beneficiario (según la fecha, tiene ' . $edadReal . ' años).',
                ])->withInput();
            }
        }

        if ($request->origen_beneficiario === 'nuevo') {
            $cotizanteDelBeneficiario = Beneficiario::find($request->nuevo_cc_cotizante);
            if (!$cotizanteDelBeneficiario
                || (string) $cotizanteDelBeneficiario->cc_afiliado !== (string) $request->cc_usuario) {
                return back()->withErrors([
                    'nuevo_cc_cotizante' => 'El cotizante del nuevo beneficiario debe ser el mismo seleccionado en la afiliación.',
                ])->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $ccAfiliado = $request->origen_beneficiario === 'existente'
                ? (int) $request->cc_afiliado
                : (int) $request->nuevo_cc_afiliado;

            // Si se afilia a una persona nueva, se crea el beneficiario + su cuenta.
            if ($request->origen_beneficiario === 'nuevo') {
                $cotizante = Beneficiario::findOrFail($request->nuevo_cc_cotizante);
                $rutaFoto = null;
                if ($request->hasFile('nuevo_foto')) {
                    $rutaFoto = $request->file('nuevo_foto')->store('usuarios', 'public');
                }

                Beneficiario::create([
                    'cc_afiliado'      => $request->nuevo_cc_afiliado,
                    'tipo_persona'     => 'beneficiario',
                    'cc_cotizante'     => $request->nuevo_cc_cotizante,
                    'parentesco'       => $request->nuevo_parentesco,
                    'nit_eps'          => $cotizante->nit_eps,
                    'nombres'          => $request->nuevo_nombres,
                    'apellidos'        => $request->nuevo_apellidos,
                    'edad'             => $request->nuevo_edad,
                    'telefono'         => $request->nuevo_telefono,
                    'direccion'        => $request->nuevo_direccion,
                    'correo'           => $request->nuevo_correo,
                    'fecha_nacimiento' => $request->nuevo_fecha_nacimiento,
                ]);

                Usuario::create([
                    'cedula'                => $request->nuevo_cc_afiliado,
                    'nombres'               => $request->nuevo_nombres,
                    'apellidos'             => $request->nuevo_apellidos,
                    'edad'                  => $request->nuevo_edad,
                    'telefono'              => $request->nuevo_telefono,
                    'correo'                => $request->nuevo_correo,
                    'foto'                  => $rutaFoto,
                    'contrasena'            => bcrypt(str()->random(40)),
                    'tipo_usuario_id'       => 2,
                    'activo'                => true,
                    'debe_crear_contrasena' => true,
                ]);
            }

            $derivados = $this->datosDelBeneficiario($ccAfiliado, $request->cc_usuario);
            if (is_string($derivados)) {
                DB::rollBack();
                return back()->withErrors(['cc_afiliado' => $derivados])->withInput();
            }

            Afiliacion::create(array_merge([
                'fecha_afiliacion'   => $request->fecha_afiliacion,
                'id_tipo_afiliacion' => $request->id_tipo_afiliacion,
                'cc_usuario'         => $request->cc_usuario,
                'id_estado'          => $request->id_estado,
                'cc_afiliado'        => $ccAfiliado,

                // Campos adicionales de afiliación
                'id_tipo_documento'  => $request->id_tipo_documento,
                'pais_expedicion'    => $request->pais_expedicion,
                'genero'             => $request->genero,
                'estado_civil'       => $request->estado_civil,
                'grupo_etnico'       => $request->grupo_etnico,
                'tiene_discapacidad' => $request->boolean('tiene_discapacidad'),
                'tipo_discapacidad'  => $request->tipo_discapacidad,
                'nacionalidad'       => $request->nacionalidad,
                'pais_nacimiento'    => $request->pais_nacimiento,
                'zona_residencia'    => $request->zona_residencia,
                'telefono_fijo'      => $request->telefono_fijo,
                'estrato'            => $request->estrato,
                'nivel_educativo'    => $request->nivel_educativo,
                'ocupacion'          => $request->ocupacion === 'Otro' ? $request->ocupacion_otra : $request->ocupacion,
                'tipo_sangre'        => $request->tipo_sangre,
                'victima_conflicto_armado' => $request->boolean('victima_conflicto_armado'),
                'numero_afiliacion_anterior' => $request->numero_afiliacion_anterior,
            ], $derivados));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($request->origen_beneficiario === 'nuevo') {
            $cotizanteNombre = $request->nuevo_cc_cotizante
                ? trim(Beneficiario::find($request->nuevo_cc_cotizante)?->nombres ?? '' . ' ' . Beneficiario::find($request->nuevo_cc_cotizante)?->apellidos ?? '')
                : null;

            return redirect()->route('beneficiarios.creado')->with([
                'exito' => 'Afiliación y beneficiario registrados correctamente.',
                'credenciales' => [
                    'cc_afiliado'  => $request->nuevo_cc_afiliado,
                    'nombres'      => trim($request->nuevo_nombres . ' ' . $request->nuevo_apellidos),
                    'correo'       => $request->nuevo_correo,
                    'tipo_persona' => 'beneficiario',
                    'cotizante'    => $cotizanteNombre,
                    'parentesco'   => $request->nuevo_parentesco,
                    'eps'          => optional(Beneficiario::find($request->nuevo_cc_cotizante)?->eps)->nombre_eps,
                ],
            ]);
        }

        return redirect()->route('afiliaciones.index')
            ->with('exito', 'Afiliación creada correctamente.');
    }

    public function edit($id)
    {
        $afiliacion = Afiliacion::findOrFail($id);
        $beneficiarios = Beneficiario::all();
        $estados = Estado::all();
        $usuarios = $this->cotizantesComoUsuarios($afiliacion->cc_usuario);
        $tipos = TipoAfiliacion::all();
        $tiposDocumento = TipoDocumento::orderBy('tipo_documento')->get();
        $parentescos = Parentesco::orderBy('id_parentesco')->get();
        $seccionActiva = 'afiliaciones';

        $generos = ['Masculino' => 'Masculino', 'Femenino' => 'Femenino', 'Otro' => 'Otro'];
        $estadosCiviles = ['Soltero' => 'Soltero', 'Casado' => 'Casado', 'Unión libre' => 'Unión libre', 'Divorciado' => 'Divorciado', 'Viudo' => 'Viudo'];
        $gruposEtnicos = ['Ninguno' => 'Ninguno', 'Indígena' => 'Indígena', 'Afrodescendiente' => 'Afrodescendiente', 'Raizal' => 'Raizal', 'Palenquero' => 'Palenquero', 'Gitano' => 'Gitano', 'Otro' => 'Otro'];
        $zonasResidencia = ['Urbana' => 'Urbana', 'Rural' => 'Rural'];
        $estratos = range(1, 6);
        $nivelesEducativos = ['Ninguno' => 'Ninguno', 'Primaria' => 'Primaria', 'Secundaria' => 'Secundaria', 'Técnica' => 'Técnica', 'Tecnológica' => 'Tecnológica', 'Universitaria' => 'Universitaria', 'Posgrado' => 'Posgrado'];
        $tiposSangre = ['O+' => 'O+', 'O-' => 'O-', 'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-'];
        $paises = ['Colombia' => 'Colombia', 'Venezuela' => 'Venezuela', 'Estados Unidos' => 'Estados Unidos', 'España' => 'España', 'México' => 'México', 'Argentina' => 'Argentina', 'Chile' => 'Chile', 'Perú' => 'Perú', 'Ecuador' => 'Ecuador', 'Otro' => 'Otro'];
        $ocupaciones = [
            'Agricultor' => 'Agricultor',
            'Comerciante' => 'Comerciante',
            'Conductor' => 'Conductor',
            'Contador' => 'Contador',
            'Ingeniero' => 'Ingeniero',
            'Médico' => 'Médico',
            'Enfermero' => 'Enfermero',
            'Profesor' => 'Profesor',
            'Abogado' => 'Abogado',
            'Técnico' => 'Técnico',
            'Obrero' => 'Obrero',
            'Empleado' => 'Empleado',
            'Estudiante' => 'Estudiante',
            'Ama de casa' => 'Ama de casa',
            'Pensionado' => 'Pensionado',
            'Desempleado' => 'Desempleado',
            'Otro' => 'Otro',
        ];

        return view('admin.afiliaciones.afiliacion.edit', compact(
            'afiliacion', 'beneficiarios', 'estados', 'usuarios', 'tipos', 'tiposDocumento',
            'parentescos', 'generos', 'estadosCiviles', 'gruposEtnicos', 'zonasResidencia',
            'estratos', 'nivelesEducativos', 'tiposSangre', 'paises', 'ocupaciones', 'seccionActiva'
        ));
    }

    public function detalleLista()
    {
        // Agrupa las afiliaciones por cotizante (núcleo familiar),
        // mostrando solo los que tienen más de una persona afiliada.
        $grupos = Afiliacion::select('cc_usuario')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('cc_usuario')
            ->having('total', '>', 1)
            ->orderByDesc('total')
            ->get();

        $nucleos = [];
        foreach ($grupos as $g) {
            $afiliaciones = Afiliacion::with(['usuario', 'eps'])
                ->where('cc_usuario', $g->cc_usuario)
                ->orderBy('created_at')
                ->get();
            if ($afiliaciones->isNotEmpty()) {
                $nucleos[] = [
                    'cc_usuario'    => $g->cc_usuario,
                    'cotizante'     => $afiliaciones->first()->usuario,
                    'eps'           => $afiliaciones->first()->eps,
                    'total'         => $afiliaciones->count(),
                    'representante' => $afiliaciones->first(),
                ];
            }
        }

        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.afiliacion.detalle_lista', compact('nucleos', 'seccionActiva'));
    }

    public function detalle($id)
    {
        $principal = Afiliacion::with(['beneficiario', 'eps', 'usuario', 'estado', 'tipoAfiliacion'])->findOrFail($id);
        $familia = Afiliacion::with(['beneficiario', 'eps', 'usuario', 'estado', 'tipoAfiliacion'])
            ->where('cc_usuario', $principal->cc_usuario)
            ->orderBy('created_at')
            ->get();

        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.afiliacion.detalle', compact('principal', 'familia', 'seccionActiva'));
    }

    public function update(Request $request, $id)
    {
        $afiliacion = Afiliacion::findOrFail($id);

        $datos = $request->validate([
            'fecha_afiliacion'   => 'required|date',
            'id_tipo_afiliacion' => 'required|exists:regimen,id_regimen',
            'cc_usuario'         => 'required|exists:usuarios,cedula',
            'id_estado'          => 'required|exists:estado,id_estado',
            'cc_afiliado'        => 'required|exists:beneficiarios,cc_afiliado',

            // Datos del cotizante (titular de la afiliación)
            'nombres_cotizante'  => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellidos_cotizante'=> ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'correo_cotizante'   => ['required', 'email', 'max:100', 'unique:usuarios,correo,' . $afiliacion->cc_usuario . ',cedula'],
            'telefono_cotizante' => ['required', 'string', 'max:20', 'regex:/^[+()\d\s\-]{7,20}$/'],

            // Campos adicionales de afiliación
            'id_tipo_documento'  => ['nullable', 'exists:tipo_documento,id_tipo_documento'],
            'pais_expedicion'    => ['nullable', 'string', 'max:100'],
            'genero'             => ['nullable', 'string', 'max:20', 'in:Masculino,Femenino,Otro'],
            'estado_civil'       => ['nullable', 'string', 'max:50'],
            'grupo_etnico'       => ['nullable', 'string', 'max:50'],
            'tiene_discapacidad' => ['nullable', 'boolean'],
            'tipo_discapacidad'  => ['nullable', 'string', 'max:100', 'required_if:tiene_discapacidad,1'],
            'nacionalidad'       => ['nullable', 'string', 'max:100'],
            'pais_nacimiento'    => ['nullable', 'string', 'max:100'],
            'zona_residencia'    => ['nullable', 'string', 'max:20', 'in:Urbana,Rural'],
            'telefono_fijo'      => ['nullable', 'string', 'max:20'],
            'estrato'            => ['nullable', 'integer', 'min:1', 'max:6'],
            'nivel_educativo'    => ['nullable', 'string', 'max:50'],
            'ocupacion'          => ['nullable', 'string', 'max:100'],
            'ocupacion_otra'     => ['nullable', 'string', 'max:100', 'required_if:ocupacion,Otro'],
            'tipo_sangre'        => ['nullable', 'string', 'max:5', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-'],
            'victima_conflicto_armado' => ['nullable', 'boolean'],
            'numero_afiliacion_anterior' => ['nullable', 'string', 'max:50'],
        ], [
            'fecha_afiliacion.required'   => 'La fecha de afiliación es obligatoria.',
            'fecha_afiliacion.date'       => 'La fecha de afiliación no es válida.',
            'id_tipo_afiliacion.required' => 'Selecciona el tipo de afiliación.',
            'cc_usuario.required'         => 'Selecciona el cotizante titular.',
            'id_estado.required'          => 'Selecciona el estado.',
            'cc_afiliado.required'        => 'Selecciona el beneficiario.',
            'nombres_cotizante.required'  => 'Los nombres del cotizante son obligatorios.',
            'apellidos_cotizante.required'=> 'Los apellidos del cotizante son obligatorios.',
            'correo_cotizante.required'   => 'El correo del cotizante es obligatorio.',
            'correo_cotizante.email'      => 'El correo del cotizante no es válido.',
            'correo_cotizante.unique'     => 'Ese correo ya está registrado para otro usuario.',
            'telefono_cotizante.required' => 'El teléfono del cotizante es obligatorio.',
            'id_tipo_documento.exists'    => 'El tipo de documento no es válido.',
            'genero.in'                   => 'El género no es válido.',
            'zona_residencia.in'          => 'La zona de residencia no es válida.',
            'estrato.integer'             => 'El estrato debe ser un número del 1 al 6.',
            'estrato.min'                 => 'El estrato debe ser entre 1 y 6.',
            'estrato.max'                 => 'El estrato debe ser entre 1 y 6.',
            'tipo_sangre.in'              => 'El tipo de sangre no es válido.',
            'tipo_discapacidad.required_if' => 'Si tiene discapacidad, indica el tipo.',
            'ocupacion_otra.required_if'    => 'Si seleccionó "Otro" en ocupación, escriba la ocupación.',
        ]);

        $derivados = $this->datosDelBeneficiario($datos['cc_afiliado'], $datos['cc_usuario']);
        if (is_string($derivados)) {
            return back()->withErrors(['cc_afiliado' => $derivados])->withInput();
        }

        DB::transaction(function () use ($afiliacion, $datos, $derivados) {
            // Actualiza la afiliación (incluyendo campos adicionales)
            $afiliacion->update(array_merge($datos, $derivados, [
                'id_tipo_documento'  => $datos['id_tipo_documento'],
                'pais_expedicion'    => $datos['pais_expedicion'],
                'genero'             => $datos['genero'],
                'estado_civil'       => $datos['estado_civil'],
                'grupo_etnico'       => $datos['grupo_etnico'],
                'tiene_discapacidad' => $request->boolean('tiene_discapacidad'),
                'tipo_discapacidad'  => $datos['tipo_discapacidad'],
                'nacionalidad'       => $datos['nacionalidad'],
                'pais_nacimiento'    => $datos['pais_nacimiento'],
                'zona_residencia'    => $datos['zona_residencia'],
                'telefono_fijo'      => $datos['telefono_fijo'],
                'estrato'            => $datos['estrato'],
                'nivel_educativo'    => $datos['nivel_educativo'],
                'ocupacion'          => $datos['ocupacion'] === 'Otro' ? $request->ocupacion_otra : $datos['ocupacion'],
                'tipo_sangre'        => $datos['tipo_sangre'],
                'victima_conflicto_armado' => $request->boolean('victima_conflicto_armado'),
                'numero_afiliacion_anterior' => $datos['numero_afiliacion_anterior'],
            ]));

            // Actualiza los datos del cotizante (usuario titular)
            $usuario = Usuario::where('cedula', $datos['cc_usuario'])->first();
            if ($usuario) {
                $usuario->update([
                    'nombres'   => $datos['nombres_cotizante'],
                    'apellidos' => $datos['apellidos_cotizante'],
                    'correo'    => $datos['correo_cotizante'],
                    'telefono'  => $datos['telefono_cotizante'],
                ]);
            }
        });

        return redirect()->route('afiliaciones.index')->with('exito', 'Afiliación actualizada correctamente.');
    }

    // Alterna entre Activo (1) e Inactivo (2)
    public function cambiarEstado($id)
    {
        $afiliacion = Afiliacion::findOrFail($id);
        $nuevoEstado = $afiliacion->id_estado == 1 ? 2 : 1;

        // Si se desactiva/activa a un cotizante (titular), el cambio se aplica
        // a todo su núcleo familiar (todas las afiliaciones con el mismo usuario).
        if ((string) $afiliacion->cc_afiliado === (string) $afiliacion->cc_usuario) {
            Afiliacion::where('cc_usuario', $afiliacion->cc_usuario)
                ->update(['id_estado' => $nuevoEstado]);
        } else {
            $afiliacion->id_estado = $nuevoEstado;
            $afiliacion->save();
        }

        $mensaje = $nuevoEstado == 1 ? 'Afiliación activada correctamente.' : 'Afiliación desactivada correctamente.';

        return redirect()->route('afiliaciones.index')->with('exito', $mensaje);
    }

    /**
     * El parentesco y la EPS no se escriben en el formulario: se toman de la
     * persona (beneficiarios). El cotizante es el titular y su EPS es la que
     * escogió; el beneficiario trae la EPS de su cotizante.
     * Devuelve un arreglo con los datos, o un texto con el error.
     */
    private function datosDelBeneficiario($ccAfiliado, $ccUsuario)
    {
        $persona = Beneficiario::findOrFail($ccAfiliado);

        // El beneficiario debe ser el propio cotizante o un familiar suyo
        $cotizanteDeLaPersona = $persona->esCotizante() ? $persona->cc_afiliado : $persona->cc_cotizante;
        if ((string) $cotizanteDeLaPersona !== (string) $ccUsuario) {
            return 'Esa persona no es beneficiaria del usuario seleccionado.';
        }

        if (!$persona->nit_eps) {
            return 'Esta persona no tiene EPS asignada. Registra primero la EPS del cotizante.';
        }

        return [
            'parentesco' => $persona->esCotizante() ? 'Titular' : ($persona->parentesco ?: 'Sin dato'),
            'nit_eps'    => $persona->nit_eps,
        ];
    }

    /**
     * Usuarios que pueden ser el titular de una afiliación: los cotizantes.
     * Al editar, se incluye también el usuario que ya tenía la afiliación.
     */
    private function cotizantesComoUsuarios($incluir = null)
    {
        $cedulas = Beneficiario::where('tipo_persona', 'cotizante')->pluck('cc_afiliado');

        return Usuario::whereIn('cedula', $cedulas)
            ->when($incluir, fn ($q) => $q->orWhere('cedula', $incluir))
            ->orderBy('nombres')
            ->get();
    }
}
