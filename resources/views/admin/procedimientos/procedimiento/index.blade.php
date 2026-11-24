@extends('admin.layout')

@section('titulo', 'Procedimientos')
@section('encabezado', 'Procedimientos')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Procedimientos</h2>
            <p class="text-sm text-gray-500">Procedimientos realizados a los pacientes, con su médico, tipo y detalle</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="boton-exportar-excel" onclick="exportarExcel()"
                    data-titulo="Listado de Procedimientos" data-nombre-archivo="procedimientos"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-50 hover:shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar Excel
            </button>
            <a href="{{ route('procedimiento.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
                @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
                Nuevo procedimiento
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 9 9 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="buscador-listado" placeholder="Buscar por paciente, médico, tipo o descripción..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <span id="contador-resultados" class="text-xs text-gray-400 font-medium"></span>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table id="tabla-listado" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">Paciente</th>
                    <th class="px-4 py-3">Médico</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3">Detalles</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($procedimientos as $procedimiento)
                    <tr class="fila-dato hover:bg-teal-50/40 transition {{ !$procedimiento->activo ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-900">
                                {{ $procedimiento->paciente ? $procedimiento->paciente->nombres . ' ' . $procedimiento->paciente->apellidos : 'Paciente no encontrado' }}
                            </span>
                            <span class="block text-xs text-gray-400">C.C. {{ $procedimiento->cedula_paciente }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-700">
                                {{ $procedimiento->medico ? $procedimiento->medico->nombres . ' ' . $procedimiento->medico->apellidos : '—' }}
                            </span>
                            <span class="block text-xs font-semibold text-gray-400">{{ $procedimiento->medico->especialidad->nombre ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full border border-teal-100">
                                {{ $procedimiento->tipoProcedimiento->nombre ?? 'Sin tipo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-700">{{ $procedimiento->fecha->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs">
                            <span class="block truncate" title="{{ $procedimiento->descripcion }}">{{ $procedimiento->descripcion }}</span>
                            @if ($procedimiento->observaciones)
                                <span class="block text-xs text-gray-400 truncate" title="{{ $procedimiento->observaciones }}">{{ $procedimiento->observaciones }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $procedimiento->detalles->count() > 0 ? 'text-blue-700 bg-blue-50' : 'text-gray-500 bg-gray-50' }} px-2.5 py-1 rounded-full">
                                {{ $procedimiento->detalles->count() }}
                                {{ $procedimiento->detalles->count() === 1 ? 'detalle' : 'detalles' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $procedimiento->activo ? 'text-green-700 bg-green-50' : 'text-gray-500 bg-gray-100' }} px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full {{ $procedimiento->activo ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $procedimiento->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @include('admin.partials.acciones_fila', [
                                'rutaEditar' => route('procedimiento.edit', $procedimiento->id),
                                'formId' => 'form-estado-procedimiento-' . $procedimiento->id,
                                'rutaEstado' => route('procedimiento.estado', $procedimiento->id),
                                'activo' => $procedimiento->activo,
                                'tituloDesactivar' => '¿Desactivar este procedimiento?',
                                'textoDesactivar' => 'El procedimiento dejará de aparecer como activo en el listado.',
                                'tituloActivar' => '¿Activar este procedimiento?',
                                'textoActivar' => 'El procedimiento volverá a aparecer como activo en el listado.',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacio">
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="inline-flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                                    @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-10 h-10 text-teal-400'])
                                </div>
                                <p class="text-base font-semibold text-gray-700">Todavía no hay procedimientos</p>
                                <p class="text-sm text-gray-400 mt-1 mb-5">Registra el primer procedimiento de un paciente.</p>
                                <a href="{{ route('procedimiento.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Crear procedimiento
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr id="fila-vacio-busqueda" class="fila-vacio oculto">
                        <td colspan="8" class="px-4 py-12 text-center">
                            <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                            <p class="text-xs text-gray-400 mt-1">No se encontraron procedimientos con ese criterio de búsqueda.</p>
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
