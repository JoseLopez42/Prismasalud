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
use App\Models\Admin\Pais;
use App\Models\Admin\Genero;
use App\Models\Admin\EstadoCivil;
use App\Models\Admin\GrupoEtnico;
use App\Models\Admin\ZonaResidencia;
use App\Models\Admin\Estrato;
use App\Models\Admin\NivelEducativo;
use App\Models\Admin\Ocupacion;
use App\Models\Admin\TipoSangre;
use App\Models\SubTipoPaciente;
use App\Models\Empresa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AfiliacionController extends Controller
{
    public function index()
    {
        $afiliaciones = Afiliacion::with(['beneficiario', 'eps', 'usuario', 'estado', 'tipoAfiliacion', 'parentesco'])->get();
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
        $empresa = $this->empresaDelAdmin();
        $seccionActiva = 'afiliaciones';

        // Catálogos para los campos adicionales (extraídos de la base de datos)
        $generos = Genero::activo()->pluck('nombre', 'id_genero');
        $estadosCiviles = EstadoCivil::activo()->pluck('nombre', 'id_estado_civil');
        $gruposEtnicos = GrupoEtnico::activo()->pluck('nombre', 'nombre');
        $zonasResidencia = ZonaResidencia::activo()->pluck('nombre', 'id_zona_residencia');
        $estratos = Estrato::activo()->pluck('numero', 'numero');
        $nivelesEducativos = NivelEducativo::activo()->pluck('nombre', 'id_nivel_educativo');
        $tiposSangre = TipoSangre::activo()->pluck('nombre', 'nombre');
        $paises = Pais::activo()->pluck('nombre', 'id_pais');
        $ocupaciones = Ocupacion::activo()->pluck('nombre', 'id_ocupacion');
        $ocupacionOtroId = $this->ocupacionOtroId();

        return view('admin.afiliaciones.afiliacion.create', compact(
            'beneficiarios', 'cotizantes', 'estados', 'usuarios', 'tipos', 'tiposDocumento',
            'parentescos', 'generos', 'estadosCiviles', 'gruposEtnicos', 'zonasResidencia',
            'estratos', 'nivelesEducativos', 'tiposSangre', 'paises', 'ocupaciones',
            'ocupacionOtroId', 'empresa', 'seccionActiva'
        ));
    }

    public function store(Request $request)
    {
        // Empresa (IPS) del administrador: ya está registrada con su NIT.
        $empresa = $this->empresaDelAdmin();

        $request->validate([
            'fecha_afiliacion'   => 'required|date',
            'id_tipo_afiliacion' => 'required|exists:regimen,id_regimen',
            'cc_usuario'         => 'required|exists:usuarios,cedula',
            'origen_beneficiario'=> 'required|in:sin,nuevo',

            // Campos adicionales del afiliado, almacenados en beneficiarios
            'id_tipo_documento'  => ['nullable', 'exists:tipo_documento,id_tipo_documento'],
            'id_pais_expedicion' => ['nullable', 'exists:paises,id_pais'],
            'id_genero'          => ['nullable', 'exists:generos,id_genero'],
            'id_estado_civil'    => ['nullable', 'exists:estados_civiles,id_estado_civil'],
            'grupo_etnico'       => ['nullable', 'string', 'max:50'],
            'tiene_discapacidad' => ['nullable', 'boolean'],
            'tipo_discapacidad'  => ['nullable', 'string', 'max:100', 'required_if:tiene_discapacidad,1'],
            'id_pais_nacionalidad' => ['nullable', 'exists:paises,id_pais'],
            'id_pais_nacimiento' => ['nullable', 'exists:paises,id_pais'],
            'id_zona_residencia' => ['nullable', 'exists:zonas_residencia,id_zona_residencia'],
            'telefono_fijo'      => ['nullable', 'string', 'max:20'],
            'estrato'            => ['nullable', 'integer', 'min:1', 'max:6'],
            'id_nivel_educativo' => ['nullable', 'exists:niveles_educativos,id_nivel_educativo'],
            'id_ocupacion'       => ['nullable', 'exists:ocupaciones,id_ocupacion'],
            'ocupacion_otra'     => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(fn () => (string) $request->input('id_ocupacion') === (string) $this->ocupacionOtroId()),
            ],
            'tipo_sangre'        => ['nullable', 'string', 'max:5', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-'],
            'victima_conflicto_armado' => ['nullable', 'boolean'],
            'numero_afiliacion_anterior' => ['nullable', 'string', 'max:50'],
        ], [
            'fecha_afiliacion.required'   => 'La fecha de afiliación es obligatoria.',
            'fecha_afiliacion.date'       => 'La fecha de afiliación no es válida.',
            'id_tipo_afiliacion.required' => 'Selecciona el tipo de afiliación.',
            'cc_usuario.required'         => 'Selecciona el cotizante titular.',
            'id_tipo_documento.exists'    => 'El tipo de documento no es válido.',
            'id_genero.exists'            => 'El género no es válido.',
            'id_zona_residencia.exists'   => 'La zona de residencia no es válida.',
            'estrato.integer'             => 'El estrato debe ser un número del 1 al 6.',
            'estrato.min'                 => 'El estrato debe ser entre 1 y 6.',
            'estrato.max'                 => 'El estrato debe ser entre 1 y 6.',
            'tipo_sangre.in'              => 'El tipo de sangre no es válido.',
            'tipo_discapacidad.required_if' => 'Si tiene discapacidad, indica el tipo.',
            'ocupacion_otra.required_if'    => 'Si seleccionó "Otro" en ocupación, escriba la ocupación.',
        ]);

        // Ruta "sin beneficiario": solo se afilia al cotizante (titular).
        if ($request->origen_beneficiario === 'sin') {
            $this->asegurarFichaCotizante((int) $request->cc_usuario);
            Beneficiario::findOrFail($request->cc_usuario)
                ->update($this->camposDemograficos($request));
        } else {
            // Ruta "nuevo beneficiario": se crea desde aquí, dentro de la afiliación.
            $request->validate([
                'nuevo_cc_cotizante'     => ['required', 'integer', 'exists:beneficiarios,cc_afiliado,tipo_persona,cotizante'],
                'nuevo_id_parentesco'    => ['required', 'exists:parentescos,id_parentesco'],
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
                'nuevo_id_parentesco.required'    => 'Selecciona el parentesco del nuevo beneficiario.',
                'nuevo_id_parentesco.exists'      => 'Selecciona un parentesco de la lista.',
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
            // Sin beneficiario: la persona afiliada es el propio cotizante.
            $ccAfiliado = $request->origen_beneficiario === 'sin'
                ? (int) $request->cc_usuario
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
                    'id_parentesco'    => $request->nuevo_id_parentesco,
                    'nit_eps'          => $cotizante->nit_eps,
                    'nombres'          => $request->nuevo_nombres,
                    'apellidos'        => $request->nuevo_apellidos,
                    'edad'             => $request->nuevo_edad,
                    'telefono'         => $request->nuevo_telefono,
                    'direccion'        => $request->nuevo_direccion,
                    'correo'           => $request->nuevo_correo,
                    'fecha_nacimiento' => $request->nuevo_fecha_nacimiento,
                ] + $this->camposDemograficos($request));

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
                    // Los pacientes que se crean desde el módulo de Afiliación
                    // son BENEFICIARIOS (dependen de un cotizante).
                    'id_sub_tipo_paciente'  => SubTipoPaciente::idPorNombre('Beneficiario'),
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
                // Toda afiliación nace Activa. El estado se cambia después
                // desde el panel de Afiliaciones (Activar / Desactivar).
                'id_estado'          => $this->estadoActivoId(),
                'cc_afiliado'        => $ccAfiliado,

                // La empresa (IPS) del administrador ya está registrada con su
                // NIT, así que no se pide: se toma de su licencia.
                'nit_empresa'        => $empresa['nit'],
                'nombre_empresa'     => $empresa['nombre'],
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
                    'parentesco'   => Parentesco::find($request->nuevo_id_parentesco)?->nombre,
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
        $empresa = $this->empresaDelAdmin();
        $seccionActiva = 'afiliaciones';

        // Catálogos para los campos adicionales (extraídos de la base de datos)
        $generos = Genero::activo()->pluck('nombre', 'id_genero');
        $estadosCiviles = EstadoCivil::activo()->pluck('nombre', 'id_estado_civil');
        $gruposEtnicos = GrupoEtnico::activo()->pluck('nombre', 'nombre');
        $zonasResidencia = ZonaResidencia::activo()->pluck('nombre', 'id_zona_residencia');
        $estratos = Estrato::activo()->pluck('numero', 'numero');
        $nivelesEducativos = NivelEducativo::activo()->pluck('nombre', 'id_nivel_educativo');
        $tiposSangre = TipoSangre::activo()->pluck('nombre', 'nombre');
        $paises = Pais::activo()->pluck('nombre', 'id_pais');
        $ocupaciones = Ocupacion::activo()->pluck('nombre', 'id_ocupacion');
        $ocupacionOtroId = $this->ocupacionOtroId();

        return view('admin.afiliaciones.afiliacion.edit', compact(
            'afiliacion', 'beneficiarios', 'estados', 'usuarios', 'tipos', 'tiposDocumento',
            'parentescos', 'generos', 'estadosCiviles', 'gruposEtnicos', 'zonasResidencia',
            'estratos', 'nivelesEducativos', 'tiposSangre', 'paises', 'ocupaciones',
            'ocupacionOtroId', 'empresa', 'seccionActiva'
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
        $principal = Afiliacion::with(['beneficiario', 'eps', 'usuario', 'estado', 'tipoAfiliacion', 'parentesco'])->findOrFail($id);
        $familia = Afiliacion::with(['beneficiario', 'eps', 'usuario', 'estado', 'tipoAfiliacion', 'parentesco'])
            ->where('cc_usuario', $principal->cc_usuario)
            ->orderBy('created_at')
            ->get();

        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.afiliacion.detalle', compact('principal', 'familia', 'seccionActiva'));
    }

    public function update(Request $request, $id)
    {
        $afiliacion = Afiliacion::findOrFail($id);

        // Aquí solo se editan los datos de la afiliación. Los datos del
        // usuario, del beneficiario y los datos personales del afiliado se
        // editan en sus propios módulos.
        $datos = $request->validate([
            'fecha_afiliacion'   => 'required|date',
            'id_tipo_afiliacion' => 'required|exists:regimen,id_regimen',
            'cc_usuario'         => 'required|exists:usuarios,cedula',
            'cc_afiliado'        => 'nullable|exists:beneficiarios,cc_afiliado',
        ], [
            'fecha_afiliacion.required'   => 'La fecha de afiliación es obligatoria.',
            'fecha_afiliacion.date'       => 'La fecha de afiliación no es válida.',
            'id_tipo_afiliacion.required' => 'Selecciona el tipo de afiliación.',
            'cc_usuario.required'         => 'Selecciona el cotizante titular.',
        ]);

        // Sin beneficiario: la persona afiliada es el propio cotizante.
        $ccAfiliado = $datos['cc_afiliado'] ?: (int) $datos['cc_usuario'];

        if (empty($datos['cc_afiliado'])) {
            $this->asegurarFichaCotizante((int) $datos['cc_usuario']);
        }

        $derivados = $this->datosDelBeneficiario($ccAfiliado, $datos['cc_usuario']);
        if (is_string($derivados)) {
            return back()->withErrors(['cc_afiliado' => $derivados])->withInput();
        }

        $datos['cc_afiliado'] = $ccAfiliado;

        DB::transaction(function () use ($afiliacion, $datos, $derivados) {
            $afiliacion->update(array_merge($datos, $derivados));
        });

        return redirect()->route('afiliaciones.index')->with('exito', 'Afiliación actualizada correctamente.');
    }
    // Alterna entre Activo (1) e Inactivo (2).
    // Solo cambia ESTA afiliación: ni los usuarios ni los demás
    // beneficiarios del núcleo se desactivan.
    public function cambiarEstado($id)
    {
        $afiliacion = Afiliacion::findOrFail($id);
        $activo = $this->estadoActivoId();
        $nuevoEstado = $afiliacion->id_estado == $activo
            ? $this->estadoId('Inactivo', 2)
            : $activo;

        $afiliacion->id_estado = $nuevoEstado;
        $afiliacion->save();

        $mensaje = $nuevoEstado == $activo ? 'Afiliación activada correctamente.' : 'Afiliación desactivada correctamente.';

        // Se vuelve a la página desde la que se accionó (listado o detalle
        // del núcleo), para no perder el contexto de la persona cambiada.
        return back()->with('exito', $mensaje);
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

        return [
            'id_parentesco' => $persona->esCotizante() ? null : $persona->id_parentesco,
            // La EPS es la de la persona. Si no tiene EPS asignada, la afiliación
            // queda con el NIT de la empresa que la registra.
            'nit_eps'    => $persona->nit_eps,
        ];
    }

    /**
     * Id de un estado del catálogo de estados de afiliación.
     */
    private function estadoId(string $nombre, int $porDefecto = 1): int
    {
        $id = Estado::where('estado', $nombre)->value('id_estado');

        return $id ? (int) $id : $porDefecto;
    }

    /**
     * Id del estado "Activo" del catálogo de estados de afiliación.
     * Toda afiliación se crea activa.
     */
    private function estadoActivoId(): int
    {
        return $this->estadoId('Activo');
    }

    /**
     * Datos de la empresa (IPS) del administrador que está en el panel.
     * La empresa ya quedó registrada con su NIT al comprar el plan, así que
     * no hay que escribirla en el formulario de afiliación.
     */
    private function empresaDelAdmin(): array
    {
        $cedula = $_SESSION['usuario_cedula'] ?? null;

        if ($cedula) {
            $licencia = \App\Models\Usuario::find($cedula)?->licencia;

            if ($licencia && $licencia->nit) {
                return [
                    'nit'    => $licencia->nit,
                    'nombre' => $licencia->nombre_ips,
                ];
            }

            $empresa = Empresa::where('usuario_cedula', $cedula)->orderByDesc('id')->first();

            if ($empresa) {
                return [
                    'nit'    => $empresa->nit,
                    'nombre' => $empresa->nombre,
                ];
            }
        }

        return ['nit' => null, 'nombre' => null];
    }

    /**
     * El usuario que se afilia sin beneficiario es el cotizante (titular).
     * Si todavía no tiene ficha en "beneficiarios", se le crea una para que
     * la afiliación pueda registrarse.
     */
    private function asegurarFichaCotizante(int $ccUsuario): void
    {
        if (Beneficiario::find($ccUsuario)) {
            return;
        }

        $usuario = \App\Models\Usuario::find($ccUsuario);

        if (! $usuario) {
            return;
        }

        Beneficiario::create([
            'cc_afiliado'  => $ccUsuario,
            'tipo_persona' => 'cotizante',
            'cc_cotizante'  => null,
            'id_parentesco' => null,
            'nit_eps'       => null,
            'nombres'      => $usuario->nombres,
            'apellidos'    => $usuario->apellidos,
            'edad'         => $usuario->edad,
            'telefono'     => $usuario->telefono,
            'direccion'    => '',
            'correo'       => $usuario->correo,
            'fecha_nacimiento' => null,
        ]);
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

    private function camposDemograficos(Request $request): array
    {
        $campos = [
            'id_tipo_documento' => 'id_tipo_documento',
            'id_pais_expedicion' => 'id_pais_expedicion',
            'id_genero' => 'id_genero',
            'id_estado_civil' => 'id_estado_civil',
            'grupo_etnico' => 'grupo_etnico',
            'tiene_discapacidad' => 'tiene_discapacidad',
            'tipo_discapacidad' => 'tipo_discapacidad',
            'id_pais_nacionalidad' => 'id_pais_nacionalidad',
            'id_pais_nacimiento' => 'id_pais_nacimiento',
            'id_zona_residencia' => 'id_zona_residencia',
            'telefono_fijo' => 'telefono_fijo',
            'estrato' => 'estrato',
            'id_nivel_educativo' => 'id_nivel_educativo',
            'id_ocupacion' => 'id_ocupacion',
            'tipo_sangre' => 'tipo_sangre',
            'victima_conflicto_armado' => 'victima_conflicto_armado',
            'numero_afiliacion_anterior' => 'numero_afiliacion_anterior',
        ];

        $datos = [];

        foreach ($campos as $input => $columna) {
            if (!$request->exists($input)) {
                continue;
            }

            $valor = $request->input($input);
            $datos[$columna] = $valor === '' ? null : $valor;
        }

        if ($request->exists('id_ocupacion')) {
            $esOtro = (string) $request->input('id_ocupacion') === (string) $this->ocupacionOtroId();
            $datos['ocupacion_otra'] = $esOtro && $request->filled('ocupacion_otra')
                ? $request->input('ocupacion_otra')
                : null;
        }

        return $datos;
    }

    private function ocupacionOtroId()
    {
        return Ocupacion::where('nombre', 'Otro')->value('id_ocupacion');
    }
}
