@extends('admin.layout')

@section('titulo', 'API Medicamentos')
@section('encabezado', 'API · Medicamentos de Colombia')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Medicamentos más comunes en Colombia</h2>
            <p class="text-sm text-gray-500">
                Fuente: Invima — Código Único de Medicamentos (API de datos.gov.co).
                Los {{ $limite }} medicamentos genéricos más comunes. Cada presentación tiene su propio código.
            </p>
        </div>
    </div>

    {{-- Buscador (consulta la API con lo que se escriba) --}}
    <form method="GET" action="{{ route('api.medicamentos') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="relative sm:w-80">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar: acetaminofen, losartan, dolex..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
            Buscar
        </button>
        @if ($buscar !== '')
            <a href="{{ route('api.medicamentos') }}" class="text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
        @endif
    </form>

    {{-- DEPURACION: si la API falla, mostramos el motivo exacto --}}
    @if ($error)
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-3 mb-4">
            <p class="font-bold">⚠️ Error en la peticion:</p>
            <p><strong>Codigo HTTP:</strong> {{ $error['codigo'] }}</p>
            <p><strong>Detalle cURL:</strong> {{ $error['detalle'] }}</p>
            <p class="break-all"><strong>URL consultada:</strong> <code>{{ $error['url'] }}</code></p>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Presentación</th>
                    <th class="px-4 py-3">Descripción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($paginador as $med)
                    <tr class="hover:bg-teal-50/40 transition">
                        <td class="px-4 py-3 text-gray-400">{{ $posicionInicial + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center font-mono text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-100">
                                {{ $med['codigo'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $med['nombre'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $med['presentacion'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $med['descripcion'] ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center text-gray-400">
                            No se encontraron medicamentos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginacion --}}
    @if ($paginador->total() > 0)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
            <p class="text-xs text-gray-500">
                Página {{ $paginador->currentPage() }} de {{ $paginador->lastPage() }}
                · {{ $paginador->total() }} presentaciones
            </p>
            {{ $paginador->onEachSide(1)->links() }}
        </div>
    @endif

@endsection