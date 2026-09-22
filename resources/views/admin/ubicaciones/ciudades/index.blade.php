@extends('admin.layout')

@section('titulo', 'Ciudades')
@section('encabezado', 'Ubicaciones · Ciudades')

@section('contenido')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Ciudades</h2>
            <p class="text-sm text-gray-500">Ciudades registradas, agrupadas por departamento</p>
        </div>
        <a href="{{ route('ciudades.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nueva ciudad
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Ciudad</th>
                    <th class="px-4 py-3">Departamento</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- $ciudades viene con el departamento ya cargado (with) --}}
                @forelse ($ciudades as $ciudad)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">#{{ $ciudad->id_ciudad }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $ciudad->ciudad }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ciudad->departamento->nombre_departamento ?? '—' }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('ciudades.edit', $ciudad->id_ciudad) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form action="{{ route('ciudades.destroy', $ciudad->id_ciudad) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar esta ciudad?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay ciudades registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection