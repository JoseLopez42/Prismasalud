@extends('admin.layout')

@section('titulo', 'Departamentos')
@section('encabezado', 'Ubicaciones · Departamentos')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Departamentos</h2>
            <p class="text-sm text-gray-500">Base para las ciudades y sedes de PrismaSalud IPS</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="boton-exportar-excel" onclick="exportarExcel()"
                    data-titulo="Listado de Departamentos" data-nombre-archivo="departamentos"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-50 hover:shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar Excel
            </button>
            <a href="{{ route('departamentos.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo departamento
            </a>
        </div>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    {{-- Buscador rápido --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
        <div class="relative sm:w-80">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="buscador-listado" placeholder="Buscar por nombre del departamento..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <span id="contador-resultados" class="text-xs text-gray-400 font-medium"></span>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto max-w-2xl">
        <table id="tabla-listado" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($departamentos as $departamento)
                    <tr class="fila-dato hover:bg-teal-50/40 transition {{ !$departamento->activo ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-500">#{{ $departamento->id_departamento }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l5.447-2.724A1 1 0 0015 16.382V5.618a1 1 0 00-.553-.894L9 7m0 13V7m6 13l5.447-2.724A1 1 0 0021 16.382V5.618a1 1 0 00-.553-.894L15 7" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $departamento->nombre_departamento }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($departamento->activo)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @include('admin.partials.acciones_fila', [
                                'rutaEditar' => route('departamentos.edit', $departamento->id_departamento),
                                'formId' => 'form-estado-departamento-' . $departamento->id_departamento,
                                'rutaEstado' => route('departamentos.estado', $departamento->id_departamento),
                                'activo' => $departamento->activo,
                                'tituloDesactivar' => '¿Desactivar este departamento?',
                                'textoDesactivar' => 'Ya no se podrá usar para nuevas ciudades.',
                                'tituloActivar' => '¿Activar este departamento?',
                                'textoActivar' => 'Volverá a estar disponible para nuevas ciudades.',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacio">
                        <td colspan="4" class="px-4 py-16 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l5.447-2.724A1 1 0 0015 16.382V5.618a1 1 0 00-.553-.894L9 7m0 13V7m6 13l5.447-2.724A1 1 0 0021 16.382V5.618a1 1 0 00-.553-.894L15 7" />
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-700">Todavía no hay departamentos</p>
                                <p class="text-sm text-gray-400 mt-1 mb-5">Crea el primer departamento para agrupar ciudades y sedes.</p>
                                <a href="{{ route('departamentos.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Crear departamento
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr id="fila-vacio-busqueda" class="fila-vacio oculto">
                        <td colspan="4" class="px-4 py-12 text-center">
                            <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                            <p class="text-xs text-gray-400 mt-1">No se encontraron departamentos con ese criterio de búsqueda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.modal_confirmacion')

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    @include('admin.partials.script_listado')
@endpush