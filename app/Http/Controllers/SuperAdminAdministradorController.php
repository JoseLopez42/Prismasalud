<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Administradores de cada empresa (tabla "usuarios", rol Administrador).
 * Los crea el Superadmin: el administrador luego entra por el login normal.
 */
class SuperAdminAdministradorController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = trim((string) $request->query('q', ''));
        $empresa = (string) $request->query('empresa', '');
        $estado = (string) $request->query('estado', '');

        $consulta = DB::table('usuarios as u')
            ->leftJoin('licencias as l', 'l.id', '=', 'u.licencia_id')
            ->where('u.tipo_usuario_id', $this->tipoAdministrador())
            ->select('u.cedula', 'u.nombres', 'u.apellidos', 'u.correo', 'u.telefono', 'u.activo',
                     'u.created_at', 'u.licencia_id', 'l.nombre_ips')
            ->orderByDesc('u.created_at');

        if ($busqueda !== '') {
            $consulta->where(function ($q) use ($busqueda) {
                $q->where('u.nombres', 'like', "%{$busqueda}%")
                  ->orWhere('u.apellidos', 'like', "%{$busqueda}%")
                  ->orWhere('u.correo', 'like', "%{$busqueda}%")
                  ->orWhere('u.cedula', 'like', "%{$busqueda}%");
            });
        }
        if ($empresa === 'sin') {
            $consulta->whereNull('u.licencia_id');
        } elseif ($empresa !== '' && ctype_digit($empresa)) {
            $consulta->where('u.licencia_id', (int) $empresa);
        }
        if ($estado === 'activo') { $consulta->where('u.activo', 1); }
        if ($estado === 'inactivo') { $consulta->where('u.activo', 0); }

        return view('superadmin.administradores.index', [
            'administradores' => $consulta->paginate(10)->withQueryString(),
            'empresas' => Licencia::orderBy('nombre_ips')->get(['id', 'nombre_ips']),
            'busqueda' => $busqueda,
            'empresa' => $empresa,
            'estado' => $estado,
        ]);
    }

    public function create(Request $request)
    {
        return view('superadmin.administradores.form', [
            'admin' => null,
            'empresas' => Licencia::orderBy('nombre_ips')->get(['id', 'nombre_ips']),
            'empresaPreseleccionada' => $request->query('empresa'),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate(
            $this->reglas(true),
            $this->mensajes()
        );

        DB::table('usuarios')->insert([
            'cedula' => $datos['cedula'],
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono' => $datos['telefono'] ?? null,
            'correo' => $datos['correo'],
            'contrasena' => Hash::make($datos['password']),
            'tipo_usuario_id' => $this->tipoAdministrador(),
            'licencia_id' => $datos['licencia_id'],
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('superadmin.administradores.index')
            ->with('exito', 'Administrador creado. Ya puede entrar por el login normal con su correo y contraseña.');
    }

    public function edit($cedula)
    {
        return view('superadmin.administradores.form', [
            'admin' => $this->buscar($cedula),
            'empresas' => Licencia::orderBy('nombre_ips')->get(['id', 'nombre_ips']),
            'empresaPreseleccionada' => null,
        ]);
    }

    public function update(Request $request, $cedula)
    {
        $admin = $this->buscar($cedula);

        $datos = $request->validate(
            $this->reglas(false, $admin->cedula),
            $this->mensajes()
        );

        $cambios = [
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono' => $datos['telefono'] ?? null,
            'correo' => $datos['correo'],
            'licencia_id' => $datos['licencia_id'],
            'updated_at' => now(),
        ];

        // La contraseña solo cambia si escribieron una nueva
        if (! empty($datos['password'])) {
            $cambios['contrasena'] = Hash::make($datos['password']);
        }

        DB::table('usuarios')->where('cedula', $admin->cedula)->update($cambios);

        return redirect()->route('superadmin.administradores.index')
            ->with('exito', 'Los datos de ' . $datos['nombres'] . ' ' . $datos['apellidos'] . ' se guardaron.');
    }

    public function cambiarEstado($cedula)
    {
        $admin = $this->buscar($cedula);
        $nuevo = $admin->activo ? 0 : 1;

        DB::table('usuarios')->where('cedula', $admin->cedula)
            ->update(['activo' => $nuevo, 'updated_at' => now()]);

        return back()->with('exito', $nuevo
            ? $admin->nombres . ' quedó activo.'
            : $admin->nombres . ' quedó inactivo y ya no puede entrar.');
    }

    // ---------- Ayudas ----------
    private function tipoAdministrador(): int
    {
        return (int) (DB::table('tipos_usuario')->where('nombre', 'Administrador')->value('id') ?? 1);
    }

    private function buscar($cedula)
    {
        return DB::table('usuarios')
            ->where('cedula', $cedula)
            ->where('tipo_usuario_id', $this->tipoAdministrador())
            ->firstOrFail();
    }

    private function reglas(bool $crear, $cedulaActual = null): array
    {
        $reglas = [
            'nombres' => ['required', 'string', 'max:100', "regex:/^[\p{L}][\p{L}\s'.\-]*$/u"],
            'apellidos' => ['required', 'string', 'max:100', "regex:/^[\p{L}][\p{L}\s'.\-]*$/u"],
            'correo' => [
                'required', 'email:rfc', 'max:150',
                Rule::unique('usuarios', 'correo')->ignore($cedulaActual, 'cedula'),
            ],
            'telefono' => ['nullable', 'regex:/^[0-9+\s-]{7,20}$/'],
            'licencia_id' => ['required', 'exists:licencias,id'],
            'password' => [
                $crear ? 'required' : 'nullable', 'string', 'min:8', 'max:100', 'confirmed',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ];

        if ($crear) {
            $reglas['cedula'] = [
                'required', 'regex:/^[0-9]{5,10}$/', 'unique:usuarios,cedula',
                function ($atributo, $valor, $fallo) {
                    if ((int) $valor > 4294967295) { $fallo('La cédula es demasiado grande.'); }
                },
            ];
        }

        return $reglas;
    }

    private function mensajes(): array
    {
        return [
            'cedula.required' => 'Escribe la cédula.',
            'cedula.regex' => 'La cédula solo lleva números (entre 5 y 10).',
            'cedula.unique' => 'Ya existe un usuario con esa cédula.',
            'nombres.required' => 'Escribe los nombres.',
            'nombres.regex' => 'Los nombres solo pueden tener letras.',
            'nombres.max' => 'Los nombres no pueden tener más de 100 caracteres.',
            'apellidos.required' => 'Escribe los apellidos.',
            'apellidos.regex' => 'Los apellidos solo pueden tener letras.',
            'apellidos.max' => 'Los apellidos no pueden tener más de 100 caracteres.',
            'correo.required' => 'Escribe el correo.',
            'correo.email' => 'Escribe un correo válido, por ejemplo nombre@dominio.com.',
            'correo.max' => 'El correo no puede tener más de 150 caracteres.',
            'correo.unique' => 'Ya existe un usuario con ese correo.',
            'telefono.regex' => 'El teléfono solo lleva números (7 a 20), puede tener + o espacios.',
            'licencia_id.required' => 'Elige la empresa del administrador.',
            'licencia_id.exists' => 'Esa empresa no existe.',
            'password.required' => 'Escribe una contraseña.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.max' => 'La contraseña no puede tener más de 100 caracteres.',
            'password.confirmed' => 'Las dos contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe tener una mayúscula, una minúscula, un número y un carácter especial (por ejemplo * # ! $).',
        ];
    }
}