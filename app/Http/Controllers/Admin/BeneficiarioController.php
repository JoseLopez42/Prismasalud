<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Beneficiario;
use App\Models\Admin\Eps;
use App\Models\Admin\Parentesco;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BeneficiarioController extends Controller
{
    // Rol con el que nace la cuenta de acceso
    // (2 = Paciente, en la tabla tipos_usuario)
    private const TIPO_PACIENTE = 2;

    public function index()
    {
        $beneficiarios = Beneficiario::with(['cotizante', 'eps'])->get();
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
            'parentesco'    => null,
        ]);

        $datos = $request->validate([
            'tipo_persona'     => ['required', 'in:cotizante'],
            'nit_eps'          => ['required', 'string', 'exists:eps,nit_eps,activo,1'],
            'cc_afiliado'      => ['required', 'integer', 'digits_between:4,12', 'unique:beneficiarios,cc_afiliado', 'unique:usuarios,cedula'],
            'nombres'          => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellidos'        => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'edad'             => ['required', 'integer', 'min:0', 'max:120'],
            'telefono'         => ['required', 'string', 'max:20', 'regex:/^[+()\d\s\-]{7,20}$/'],
            'direccion'        => ['required', 'string', 'max:255'],
            'correo'           => ['required', 'email', 'max:100', 'unique:beneficiarios,correo', 'unique:usuarios,correo'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'foto'             => ['nullable', 'image', 'max:2048'], // opcional, máx 2 MB
        ], [
            'tipo_persona.required'     => 'Indica si la persona es cotizante o beneficiario.',
            'tipo_persona.in'           => 'El tipo de persona no es válido.',
            'nit_eps.required_if'       => 'Selecciona la EPS del cotizante.',
            'nit_eps.exists'            => 'La EPS seleccionada no existe o está inactiva.',
            'cc_cotizante.required_if'  => 'Selecciona de qué cotizante es familiar el beneficiario.',
            'cc_cotizante.exists'       => 'El cotizante seleccionado no existe.',
            'parentesco.required_if'    => 'Selecciona el parentesco con el cotizante.',
            'parentesco.exists'         => 'Selecciona un parentesco de la lista.',
            'cc_afiliado.required'      => 'La cédula es obligatoria.',
            'cc_afiliado.integer'       => 'La cédula debe ser un número.',
            'cc_afiliado.digits_between'=> 'La cédula debe tener entre 4 y 12 dígitos.',
            'cc_afiliado.unique'        => 'Esa cédula ya existe (como beneficiario o como usuario del sistema).',
            'nombres.required'          => 'Los nombres son obligatorios.',
            'nombres.regex'             => 'Los nombres solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'apellidos.required'        => 'Los apellidos son obligatorios.',
            'apellidos.regex'           => 'Los apellidos solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'edad.required'             => 'La edad es obligatoria.',
            'edad.integer'              => 'La edad debe ser un número entera.',
            'edad.min'                  => 'La edad no puede ser negativa.',
            'edad.max'                  => 'La edad no puede superar los 120 años.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.regex'            => 'El teléfono debe tener entre 7 y 20 caracteres (números, +, espacios, paréntesis o guiones).',
            'direccion.required'        => 'La dirección es obligatoria.',
            'correo.required'           => 'El correo es obligatorio.',
            'correo.email'              => 'El correo no tiene un formato válido.',
            'correo.unique'             => 'Ese correo ya está registrado (como beneficiario o como usuario del sistema).',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento no es una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'foto.image'                => 'La foto debe ser una imagen (jpg, png, etc.).',
            'foto.max'                  => 'La foto no puede pesar más de 2 MB.',
        ]);

        // La edad digitada debe coincidir con la fecha de nacimiento
        // (se permite diferencia de 1 año según si ya cumplió años o no).
        $edadReal = Carbon::parse($datos['fecha_nacimiento'])->age;
        if (abs((int) $datos['edad'] - $edadReal) > 1) {
            return back()->withErrors([
                'edad' => 'La edad no coincide con la fecha de nacimiento (según la fecha, tiene ' . $edadReal . ' años).',
            ])->withInput();
        }

        if ($datos['tipo_persona'] === 'cotizante') {
            // Un cotizante no depende de nadie: se limpian esos campos.
            $datos['cc_cotizante'] = null;
            $datos['parentesco']   = null;
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
                'edad'                  => $datos['edad'],
                'telefono'              => $datos['telefono'],
                'correo'                => $datos['correo'],
                'foto'                  => $rutaFoto,
                'contrasena'            => bcrypt(Str::random(40)), // relleno, nadie la conoce
                'tipo_usuario_id'       => self::TIPO_PACIENTE,
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
                'parentesco'   => $datos['parentesco'],
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
        $beneficiario = Beneficiario::findOrFail($cc);
        $seccionActiva = 'afiliaciones';
        return view('admin.afiliaciones.beneficiarios.edit', compact('beneficiario', 'seccionActiva'));
    }

    public function update(Request $request, $cc)
    {
        $beneficiario = Beneficiario::findOrFail($cc);

        $datos = $request->validate([
            'nombres'          => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'apellidos'        => ['required', 'string', 'max:100', 'regex:/^[\pL\s.\'-]+$/u'],
            'edad'             => ['required', 'integer', 'min:0', 'max:120'],
            'telefono'         => ['required', 'string', 'max:20', 'regex:/^[+()\d\s\-]{7,20}$/'],
            'direccion'        => ['required', 'string', 'max:255'],
            'correo'           => ['required', 'email', 'max:100', 'unique:beneficiarios,correo,' . $cc . ',cc_afiliado', 'unique:usuarios,correo,' . $cc . ',cedula'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'foto'             => ['nullable', 'image', 'max:2048'],
        ], [
            'nombres.required'          => 'Los nombres son obligatorios.',
            'nombres.regex'             => 'Los nombres solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'apellidos.required'        => 'Los apellidos son obligatorios.',
            'apellidos.regex'           => 'Los apellidos solo pueden contener letras, espacios, puntos, guiones y apóstrofos.',
            'edad.required'             => 'La edad es obligatoria.',
            'edad.integer'              => 'La edad debe ser un número entera.',
            'edad.min'                  => 'La edad no puede ser negativa.',
            'edad.max'                  => 'La edad no puede superar los 120 años.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.regex'            => 'El teléfono debe tener entre 7 y 20 caracteres (números, +, espacios, paréntesis o guiones).',
            'direccion.required'        => 'La dirección es obligatoria.',
            'correo.required'           => 'El correo es obligatorio.',
            'correo.email'              => 'El correo no tiene un formato válido.',
            'correo.unique'             => 'Ese correo ya está registrado (como beneficiario o como usuario del sistema).',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento no es una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'foto.image'                => 'La foto debe ser una imagen (jpg, png, etc.).',
            'foto.max'                  => 'La foto no puede pesar más de 2 MB.',
        ]);

        $edadReal = Carbon::parse($datos['fecha_nacimiento'])->age;
        if (abs((int) $datos['edad'] - $edadReal) > 1) {
            return back()->withErrors([
                'edad' => 'La edad no coincide con la fecha de nacimiento (según la fecha, tiene ' . $edadReal . ' años).',
            ])->withInput();
        }

        // Actualiza el beneficiario y, si ya tiene cuenta, la sincroniza (incluyendo foto).
        DB::transaction(function () use ($beneficiario, $datos, $request) {
            $beneficiario->update($datos);

            $usuario = Usuario::where('cedula', $beneficiario->cc_afiliado)->first();
            if ($usuario) {
                $usuario->update([
                    'nombres'   => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'edad'      => $datos['edad'],
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

        return redirect()->route('beneficiarios.index')->with('exito', 'Beneficiario actualizado correctamente.');
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