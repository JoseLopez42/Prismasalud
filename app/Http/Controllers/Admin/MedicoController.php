<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Especialidad;
use App\Models\Admin\Medico;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MedicoController extends Controller
{
    // Lista todos los médicos
    public function index()
    {
        $medicos = Medico::with('especialidad')->orderBy('apellidos')->orderBy('nombres')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.medico.index', compact('medicos', 'seccionActiva'));
    }

    // Formulario para crear un médico (aquí también nace su cuenta)
    public function create()
    {
        $especialidades = Especialidad::orderBy('nombre')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.medico.create', compact('especialidades', 'seccionActiva'));
    }

    // Guarda el nuevo médico y su cuenta de acceso
    public function store(Request $request)
    {
        $datos = $request->validate([
            'cedula'           => 'required|integer|min:1|max:2147483647|unique:medico,cedula',
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'correo'           => 'required|email|max:150|unique:medico,correo',
            'telefono'         => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:200',
            'contrasena'       => 'required|string|min:6',
            'id_especialidad'  => 'required|exists:especialidad,id',
            'foto'             => 'nullable|image|max:2048', // máx 2MB
        ]);

        // REC-03: el médico hereda la IPS del administrador en sesión.
        // El formulario NO puede enviar empresa_id: se toma de la licencia
        // del administrador (licencias.empresa_id), nunca de los datos POST.
        $empresaId = isset($_SESSION['usuario_cedula'])
            ? Usuario::find($_SESSION['usuario_cedula'])?->licencia?->empresa_id
            : null;

        if ($empresaId === null) {
            return back()->withInput()->with('error', 'No se pudo identificar la IPS del administrador en sesión.');
        }

        $datos['empresa_id'] = $empresaId;

        // Si subieron una foto, la guardamos en storage/app/public/medicos
        // (necesita "php artisan storage:link" para verse desde el navegador)
        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('medicos', 'public');
        }

        $datos['contrasena'] = Hash::make($datos['contrasena']);
        $datos['estado'] = 'activo';

        Medico::create($datos);

        return redirect()->route('medico.index')->with('exito', 'Médico creado correctamente.');
    }

    // Formulario para editar un médico existente
    public function edit($cedula)
    {
        $medico = Medico::findOrFail($cedula);
        $especialidades = Especialidad::orderBy('nombre')->get();
        $seccionActiva = 'medicos';
        return view('admin.medicos.medico.edit', compact('medico', 'especialidades', 'seccionActiva'));
    }

    // Actualiza el médico. La contraseña y la foto son opcionales:
    // si llegan vacías, se dejan las que ya tenía.
    public function update(Request $request, $cedula)
    {
        $medico = Medico::findOrFail($cedula);

        $datos = $request->validate([
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'correo'           => 'required|email|max:150|unique:medico,correo,' . $cedula . ',cedula',
            'telefono'         => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:200',
            'contrasena'       => 'nullable|string|min:6',
            'id_especialidad'  => 'required|exists:especialidad,id',
            'foto'             => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('medicos', 'public');
        }

        if (!empty($datos['contrasena'])) {
            $datos['contrasena'] = Hash::make($datos['contrasena']);
        } else {
            unset($datos['contrasena']);
        }

        $medico->update($datos);

        return redirect()->route('medico.index')->with('exito', 'Médico actualizado correctamente.');
    }

    // Activa o desactiva la cuenta del médico (en vez de eliminarlo)
    public function cambiarEstado($cedula)
    {
        $medico = Medico::findOrFail($cedula);
        $medico->estado = $medico->estado === 'activo' ? 'inactivo' : 'activo';
        $medico->save();

        $mensaje = $medico->estado === 'activo'
            ? 'Médico activado correctamente.'
            : 'Médico desactivado correctamente. Ya no podrá iniciar sesión.';

        return redirect()->route('medico.index')->with('exito', $mensaje);
    }
}
