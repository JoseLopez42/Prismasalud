@extends('admin.layout')

@section('titulo', 'Estados')
@section('encabezado', 'Afiliaciones · Estados')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Estados</h2>
            <p class="text-sm text-gray-500">Catálogo de estados de afiliación</p>
        </div>
        <a href="{{ route('estados.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo estado
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">{{ session('exito') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table id="tabla-listado" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($estados as $e)
                    <tr class="fila-dato hover:bg-teal-50/40 transition">
                        <td class="px-4 py-3 text-gray-500">#{{ $e->id_estado }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $e->estado }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('estados.edit', $e->id_estado) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 text-xs font-medium hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition">
                                Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-16 text-center text-sm text-gray-400">No hay estados registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
