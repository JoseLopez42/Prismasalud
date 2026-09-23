<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Eps;
use App\Models\Admin\Sede;
use Illuminate\Http\Request;

class EpsController extends Controller
{
    // Lista todas las EPS, mostrando el nombre de la sede
    public function index()
    {
        $eps = Eps::with('sede')->get();
        $seccionActiva = 'eps';
        return view('admin.eps.index', compact('eps', 'seccionActiva'));
    }

    // Formulario para crear una nueva EPS
    public function create()
    {
        $sedes = Sede::all();
        $seccionActiva = 'eps';
        return view('admin.eps.create', compact('sedes', 'seccionActiva'));
    }

    // Guarda la nueva EPS
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nit_eps'           => 'required|string|max:20|unique:eps,nit_eps',
            'nombre_eps'        => 'required|string|max:150',
            'telefono_contacto' => 'required|string|max:20',
            'id_sede'           => 'required|exists:sedes,id_sede',
        ]);

        Eps::create($datos);

        return redirect()->route('eps.index')->with('exito', 'EPS creada correctamente.');
    }

    // Formulario para editar una EPS existente
    public function edit($nit_eps)
    {
        $eps = Eps::findOrFail($nit_eps);
        $sedes = Sede::all();
        $seccionActiva = 'eps';
        return view('admin.eps.edit', compact('eps', 'sedes', 'seccionActiva'));
    }

    // Actualiza la EPS (el NIT no se modifica, es la PK)
    public function update(Request $request, $nit_eps)
    {
        $eps = Eps::findOrFail($nit_eps);

        $datos = $request->validate([
            'nombre_eps'        => 'required|string|max:150',
            'telefono_contacto' => 'required|string|max:20',
            'id_sede'           => 'required|exists:sedes,id_sede',
        ]);

        $eps->update($datos);

        return redirect()->route('eps.index')->with('exito', 'EPS actualizada correctamente.');
    }

    // Activa o desactiva la EPS (en vez de eliminarla)
    public function cambiarEstado($nit_eps)
    {
        $eps = Eps::findOrFail($nit_eps);
        $eps->activo = !$eps->activo;
        $eps->save();

        $mensaje = $eps->activo ? 'EPS activada correctamente.' : 'EPS desactivada correctamente.';

        return redirect()->route('eps.index')->with('exito', $mensaje);
    }
}