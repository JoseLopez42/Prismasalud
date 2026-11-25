<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Afiliacion;
use App\Models\Admin\Beneficiario;
use App\Models\Admin\Eps;
use App\Models\Admin\Parentesco;
use App\Models\Usuario;
use App\Models\SubTipoPaciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeneficiarioController extends Controller
{
    // Rol con el que nace la cuenta de acceso
    // (2 = Paciente, en la tabla tipos_usuario)
    private const TIPO_PACIENTE = 2;

    public function index()
    {
        $beneficiarios = Beneficiario::with(['cotizante', 'eps', 'parentesco'])->get();
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.beneficiarios.index', compact('beneficiarios', 'seccionActiva'));
    }

    public function create()
    {
        // Lista de cotizantes para elegir de quién es familiar el beneficiario
        $cotizantes = Beneficiario::with('eps')
            ->where('tipo_persona', 'cotizante')
            ->orderBy('nombres')
            ->get();

        // EPS activas para que el cotizante escoja la suya
        $eps = Eps::where('activo', true)->orderBy('nombre_eps')->get();
        // Opciones de parentesco (tabla parentescos)
        $parentescos = Parentesco::orderBy('id_parentesco')->get();
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.beneficiarios.create', compact('cotizantes', 'parentescos', 'eps', 'seccionActiva'));
    }

    public function store(Request $request)
    {
        // En este modulo solo se crean COTIZANTES. Los beneficiarios (familiares)
        // se registran desde el modulo de Afiliaciones. Se fuerza el tipo.
        $request->merge([
            'tipo_persona'  => 'cotizante',
            'cc_cotizante'  => null,
        ]);

        $datos = $request->validate([
            'tipo_persona'     => ['required', 'in:cotizante'],
            'nit_eps'          => ['required', 'string', 'exists:eps,nit_eps,activo,1'],
            'cc_afiliado'      => ['required', 'integer', 'digits_between:4,12', 'unique:beneficiarios,cc_afiliado', 'unique:usuarios,cedula'],
            'nombres'          => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellidos'        => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'telefono'         => ['required', 'string', 'max:20', 'regex:/^[+()\d\s\-]{7,20}$/'],
            'direccion'        => ['required', 'string', 'max:255'],
            'correo'           => ['required', 'email', 'max:100', 'unique:beneficiarios,correo', 'unique:usuarios,correo'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'foto'             => ['nullable', 'image', 'max:2048'], // opcional, máx 2 MB
        ], [
            'tipo_persona.required'     => 'Indica si la persona es cotizante o beneficiario.',
            'tipo_persona.in'           => 'El tipo de persona no es válido.',
            'nit_eps.required_if'       => 'Selecciona la EPS del cotizante.',
            'nit_eps.exists'            => 'La EPS seleccionada no existe o está inactiva.',
            'cc_cotizante.required_if'  => 'Selecciona de qué cotizante es familiar el beneficiario.',
            'cc_cotizante.exists'       => 'El cotizante seleccionado no existe.',
            'cc_afiliado.required'      => 'La cédula es obligatoria.',
            'cc_afiliado.integer'       => 'La cédula debe ser un número.',
            'cc_afiliado.digits_between'=> 'La cédula debe tener entre 4 y 12 dígitos.',
            'cc_afiliado.unique'        => 'Esa cédula ya existe (como beneficiario o como usuario del sistema).',
            'nombres.required'          => 'Los nombres son obligatorios.',
            'nombres.regex'             => 'Los nombres solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'apellidos.required'        => 'Los apellidos son obligatorios.',
            'apellidos.regex'           => 'Los apellidos solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.regex'            => 'El teléfono debe tener entre 7 y 20 caracteres (números, +, espacios, paréntesis o guiones).',
            'direccion.required'        => 'La dirección es obligatoria.',
            'correo.required'           => 'El correo es obligatorio.',
            'correo.email'              => 'El correo no tiene un formato válido.',
            'correo.unique'             => 'Ese correo ya está registrado (como beneficiario o como usuario del sistema).',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento no es una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento debe ser posterior a 1900-01-01.',
            'foto.image'                => 'La foto debe ser una imagen (jpg, png, etc.).',
            'foto.max'                  => 'La foto no puede pesar más de 2 MB.',
        ]);

        if ($datos['tipo_persona'] === 'cotizante') {
            // Un cotizante no depende de nadie: se limpian esos campos.
            $datos['cc_cotizante'] = null;
        } else {
            // El beneficiario NO elige EPS: hereda la de su cotizante.
            $datos['nit_eps'] = Beneficiario::find($datos['cc_cotizante'])?->nit_eps;
        }

        // La foto (opcional) va en usuarios.foto, no en beneficiarios.
        $rutaFoto = null;
        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('usuarios', 'public');
        }
        unset($datos['foto']);

        // Beneficiario + cuenta de acceso, todo o nada (transacción).
        // La cuenta nace SIN contraseña útil: la persona la crea ella misma
        // la primera vez que entre con su cédula (debe_crear_contrasena = true).
        DB::transaction(function () use ($datos, $rutaFoto) {
            Beneficiario::create($datos);

            Usuario::create([
                'cedula'                => $datos['cc_afiliado'],
                'nombres'               => $datos['nombres'],
                'apellidos'             => $datos['apellidos'],
                'telefono'              => $datos['telefono'],
                'correo'                => $datos['correo'],
                'foto'                  => $rutaFoto,
                'contrasena'            => bcrypt(Str::random(40)), // relleno, nadie la conoce
                'tipo_usuario_id'       => self::TIPO_PACIENTE,
                // El sub tipo de paciente depende de cómo se registró la persona.
                'id_sub_tipo_paciente'  => SubTipoPaciente::idPorNombre(
                    $datos['tipo_persona'] === 'beneficiario' ? 'Beneficiario' : 'Cotizante'
                ),
                'activo'                => true,
                'debe_crear_contrasena' => true,
            ]);
        });

        $epsNombre = $datos['nit_eps'] ? Eps::find($datos['nit_eps'])?->nombre_eps : null;

        $cotizante = null;
        if ($datos['tipo_persona'] === 'beneficiario') {
            $c = Beneficiario::find($datos['cc_cotizante']);
            $cotizante = $c ? trim($c->nombres . ' ' . $c->apellidos) : null;
        }

        return redirect()->route('beneficiarios.creado')->with([
            'exito'        => 'Persona registrada correctamente. Su cuenta de acceso ya está lista.',
            'credenciales' => [
                'cc_afiliado'  => $datos['cc_afiliado'],
                'nombres'      => trim($datos['nombres'] . ' ' . $datos['apellidos']),
                'correo'       => $datos['correo'],
                'tipo_persona' => $datos['tipo_persona'],
                'cotizante'    => $cotizante,
                'eps'          => $epsNombre,
            ],
        ]);
    }

    /**
     * Pantalla de éxito: muestra cuál es la cuenta creada
     * (usuario = cédula) y cómo debe entrar la persona la primera vez.
     */
    public function creado()
    {
        $credenciales = session('credenciales');

        if (!$credenciales) {
            return redirect()->route('beneficiarios.index');
        }

        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.beneficiarios.creado', compact('credenciales', 'seccionActiva'));
    }

    public function edit($cc)
    {
        $beneficiario = Beneficiario::with(['cotizante', 'eps'])->findOrFail($cc);

        // El cotizante (titular) elige su EPS; el beneficiario (familiar) solo
        // muestra la que hereda de su cotizante y puede cambiar el parentesco.
        $eps = Eps::query()
            ->when($beneficiario->nit_eps, function ($query) use ($beneficiario) {
                // Se conserva la EPS actual aunque esté inactiva, para que el
                // formulario pueda guardar el valor ya existente.
                $query->where('activo', true)->orWhere('nit_eps', $beneficiario->nit_eps);
            }, function ($query) {
                $query->where('activo', true);
            })
            ->orderBy('nombre_eps')
            ->get();

        $parentescos = Parentesco::orderBy('id_parentesco')->get();
        $seccionActiva = 'afiliaciones';

        return view('admin.afiliaciones.beneficiarios.edit', compact(
            'beneficiario', 'eps', 'parentescos', 'seccionActiva'
        ));
    }

    public function update(Request $request, $cc)
    {
        $beneficiario = Beneficiario::with('cotizante')->findOrFail($cc);
        $esCotizante = $beneficiario->esCotizante();

        $reglas = [
            'nombres'          => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellidos'        => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'telefono'         => ['required', 'string', 'max:20', 'regex:/^[+()\d\s\-]{7,20}$/'],
            'direccion'        => ['required', 'string', 'max:255'],
            'correo'           => ['required', 'email', 'max:100', 'unique:beneficiarios,correo,' . $cc . ',cc_afiliado', 'unique:usuarios,correo,' . $cc . ',cedula'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'foto'             => ['nullable', 'image', 'max:2048'],
        ];

        if ($esCotizante) {
            // Solo el titular puede elegir su EPS. Se permite conservar la
            // actual aunque ya esté inactiva en el catálogo.
            $reglas['nit_eps'] = [
                'nullable',
                'string',
                Rule::exists('eps', 'nit_eps')->where(function ($query) use ($beneficiario) {
                    $query->where('activo', true);

                    if ($beneficiario->nit_eps) {
                        $query->orWhere('nit_eps', $beneficiario->nit_eps);
                    }
                }),
            ];
        } else {
            // El beneficiario hereda la EPS de su cotizante; lo único propio
            // que se puede editar aquí es el parentesco.
            $reglas['id_parentesco'] = ['required', 'exists:parentescos,id_parentesco'];
        }

        $mensajes = [
            'nombres.required'          => 'Los nombres son obligatorios.',
            'nombres.regex'             => 'Los nombres solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'apellidos.required'        => 'Los apellidos son obligatorios.',
            'apellidos.regex'           => 'Los apellidos solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.regex'            => 'El teléfono debe tener entre 7 y 20 caracteres (números, +, espacios, paréntesis o guiones).',
            'direccion.required'        => 'La dirección es obligatoria.',
            'correo.required'           => 'El correo es obligatorio.',
            'correo.email'              => 'El correo no tiene un formato válido.',
            'correo.unique'             => 'Ese correo ya está registrado (como beneficiario o como usuario del sistema).',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento no es una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento debe ser posterior a 1900-01-01.',
            'foto.image'                => 'La foto debe ser una imagen (jpg, png, etc.).',
            'foto.max'                  => 'La foto no puede pesar más de 2 MB.',
        ];

        if ($esCotizante) {
            $mensajes['nit_eps.exists'] = 'La EPS seleccionada no existe o está inactiva.';
        } else {
            $mensajes['id_parentesco.required'] = 'Selecciona el parentesco con el cotizante.';
            $mensajes['id_parentesco.exists']   = 'Selecciona un parentesco de la lista.';
        }

        $datos = $request->validate($reglas, $mensajes);

        if ($esCotizante) {
            // Un cotizante es titular: no depende de nadie ni tiene parentesco.
            $datos['cc_cotizante'] = null;
        } else {
            // El beneficiario NO elige EPS: toma la de su cotizante.
            $datos['nit_eps']      = $beneficiario->cotizante?->nit_eps;
            $datos['cc_cotizante'] = $beneficiario->cc_cotizante;
        }

        $epsAnterior         = $beneficiario->nit_eps;
        $parentescoAnteriorId = $beneficiario->id_parentesco;

        // Actualiza la persona y, si ya tiene cuenta, la sincroniza (incluyendo foto).
        DB::transaction(function () use ($beneficiario, $datos, $request, $esCotizante, $epsAnterior, $parentescoAnteriorId) {
            $beneficiario->update($datos);

            // Si el cotizante cambia de EPS, sus familiares y sus afiliaciones
            // deben quedar con la misma EPS (la heredan de él).
            if ($esCotizante && $epsAnterior !== $beneficiario->nit_eps) {
                Beneficiario::where('cc_cotizante', $beneficiario->cc_afiliado)
                    ->update(['nit_eps' => $beneficiario->nit_eps]);

                Afiliacion::where('cc_usuario', $beneficiario->cc_afiliado)
                    ->when($epsAnterior, fn ($q) => $q->where('nit_eps', $epsAnterior))
                    ->update(['nit_eps' => $beneficiario->nit_eps]);
            }

            // El parentesco también se copia a la afiliación de ese familiar.
            if (!$esCotizante && $parentescoAnteriorId !== $beneficiario->id_parentesco) {
                Afiliacion::where('cc_afiliado', $beneficiario->cc_afiliado)
                    ->where('id_parentesco', $parentescoAnteriorId)
                    ->update(['id_parentesco' => $beneficiario->id_parentesco]);
            }

            $usuario = Usuario::where('cedula', $beneficiario->cc_afiliado)->first();
            if ($usuario) {
                $usuario->update([
                    'nombres'   => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'telefono'  => $datos['telefono'],
                    'correo'    => $datos['correo'],
                ]);

                // Foto: solo se cambia si suben una nueva
                if ($request->hasFile('foto')) {
                    // Elimina la foto anterior si existe
                    if ($usuario->foto && file_exists(storage_path('app/public/' . $usuario->foto))) {
                        unlink(storage_path('app/public/' . $usuario->foto));
                    }
                    $usuario->foto = $request->file('foto')->store('usuarios', 'public');
                    $usuario->save();
                }
            }
        });

        $mensaje = $esCotizante
            ? 'Cotizante actualizado correctamente.'
            : 'Beneficiario actualizado correctamente.';

        return redirect()->route('beneficiarios.index')->with('exito', $mensaje);
    }

    public function cambiarEstado($cc)
    {
        $usuario = \App\Models\Usuario::findOrFail($cc);
        $nuevoEstado = ! (bool) $usuario->activo;

        $persona = Beneficiario::find($cc);
        $esCotizante = $persona && $persona->esCotizante();

        // Si es cotizante (titular), al activar/desactivar se aplica a todo
        // su núcleo familiar (sus beneficiarios).
        $cedulas = [$cc];
        if ($esCotizante) {
            $cedulas = array_merge($cedulas, Beneficiario::where('cc_cotizante', $cc)->pluck('cc_afiliado')->all());
        }

        \App\Models\Usuario::whereIn('cedula', $cedulas)->update(['activo' => $nuevoEstado]);

        $mensaje = $nuevoEstado ? 'Persona(s) activada(s) correctamente.' : 'Persona(s) desactivada(s) correctamente.';
        return redirect()->route('beneficiarios.index')->with('exito', $mensaje);
    }
}