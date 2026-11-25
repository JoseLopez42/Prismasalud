@extends('admin.layout')

@section('titulo', 'Afiliaciones')
@section('encabezado', 'Afiliaciones · Listado')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Afiliaciones</h2>
            <p class="text-sm text-gray-500">Cada fila es una persona afiliada: el cotizante (titular) o uno de sus beneficiarios (familiares)</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="boton-exportar-excel" onclick="exportarExcel()"
                    data-titulo="Listado de Afiliaciones" data-nombre-archivo="afiliaciones"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-50 hover:shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar Excel
            </button>
            <a href="{{ route('afiliaciones.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nueva afiliación
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
            <input type="text" id="buscador-listado" placeholder="Buscar..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <span id="contador-resultados" class="text-xs text-gray-400 font-medium"></span>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
        <table id="tabla-listado" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">C.C. Cotizante</th>
                    <th class="px-4 py-3">Cotizante</th>
                    <th class="px-4 py-3">Persona afiliada</th>
                    <th class="px-4 py-3">Es</th>
                    <th class="px-4 py-3">Parentesco</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Régimen</th>
                    <th class="px-4 py-3">EPS</th>
                    <th class="px-4 py-3">Empresa</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($afiliaciones as $afiliacion)
                    <tr class="fila-dato hover:bg-teal-50/40 transition {{ $afiliacion->id_estado != 1 ? 'bg-gray-50/60' : '' }}">
                        @php $esTitular = (string) $afiliacion->cc_afiliado === (string) $afiliacion->cc_usuario; @endphp
                        <td class="px-4 py-3 text-gray-500">{{ $afiliacion->cc_usuario }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $afiliacion->usuario?->nombres }} {{ $afiliacion->usuario?->apellidos }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900">
                            {{ $afiliacion->beneficiario?->nombres }} {{ $afiliacion->beneficiario?->apellidos }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($esTitular)
                                <span class="inline-flex text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full">Cotizante</span>
                            @else
                                <span class="inline-flex text-xs font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">Beneficiario</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $esTitular ? 'Titular' : ($afiliacion->parentesco?->nombre ?? '—') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $afiliacion->fecha_afiliacion }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $afiliacion->tipoAfiliacion?->nombre_regimen }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $afiliacion->eps?->nombre_eps ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $afiliacion->nombre_empresa ?? '—' }}
                            @if ($afiliacion->nit_empresa)
                                <span class="block text-xs text-gray-400">NIT {{ $afiliacion->nit_empresa }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($afiliacion->id_estado == 1)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $afiliacion->estado?->estado ?? 'Activo' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>{{ $afiliacion->estado?->estado ?? 'Inactivo' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @include('admin.partials.acciones_fila', [
                                'rutaEditar' => route('afiliaciones.edit', $afiliacion->id_afiliacion),
                                'formId' => 'form-estado-afiliacion-' . $afiliacion->id_afiliacion,
                                'rutaEstado' => route('afiliaciones.estado', $afiliacion->id_afiliacion),
                                'activo' => $afiliacion->id_estado == 1,
                                'tituloDesactivar' => '¿Desactivar esta afiliación?',
                                'textoDesactivar' => 'La afiliación quedará inactiva.',
                                'tituloActivar' => '¿Activar esta afiliación?',
                                'textoActivar' => 'La afiliación quedará activa nuevamente.',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr class="fila-vacio">
                        <td colspan="10" class="px-4 py-16 text-center">
                            <p class="text-base font-semibold text-gray-700">Todavía no hay afiliaciones</p>
                            <p class="text-sm text-gray-400 mt-1 mb-5">Crea la primera afiliación para comenzar.</p>
                            <a href="{{ route('afiliaciones.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                                Crear afiliación
                            </a>
                        </td>
                    </tr>
                    <tr id="fila-vacio-busqueda" class="fila-vacio oculto">
                        <td colspan="10" class="px-4 py-12 text-center">
                            <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                            <p class="text-xs text-gray-400 mt-1">No se encontraron afiliaciones con ese criterio de búsqueda.</p>
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