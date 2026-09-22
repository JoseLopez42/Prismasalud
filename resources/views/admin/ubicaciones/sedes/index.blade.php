@extends('admin.layout')

@section('titulo', 'Sedes')
@section('encabezado', 'Ubicaciones · Sedes')

@section('contenido')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sedes</h2>
            <p class="text-sm text-gray-500">Sedes físicas de PrismaSalud IPS, agrupadas por ciudad</p>
        </div>
        <a href="{{ route('sedes.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
            + Nueva sede
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
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Dirección</th>
                    <th class="px-4 py-3">Teléfono</th>
                    <th class="px-4 py-3">Ciudad</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sedes as $sede)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">#{{ $sede->id_sede }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $sede->nombre_sede }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sede->direccion }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sede->telefono }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sede->ciudad->ciudad ?? '—' }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('sedes.edit', $sede->id_sede) }}"
                               class="text-teal-600 hover:underline font-medium">Editar</a>

                            <form action="{{ route('sedes.destroy', $sede->id_sede) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar esta sede?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            Todavía no hay sedes registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection