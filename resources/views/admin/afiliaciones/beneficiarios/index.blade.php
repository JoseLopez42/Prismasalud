@extends('admin.layout')

@section('titulo', 'Beneficiarios')
@section('encabezado', 'Afiliaciones · Beneficiarios')

@section('contenido')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 id="titulo-listado" class="text-xl font-bold text-gray-900">Beneficiarios</h2>
            <p class="text-sm text-gray-500">Personas afiliadas al sistema de salud, separadas por cotizantes y beneficiarios</p>
        </div>
        <a href="{{ route('beneficiarios.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo cotizante
        </a>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">{{ session('exito') }}</div>
    @endif

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

    @php
        $cotizantes = $beneficiarios->where('tipo_persona', 'cotizante');
        $familiares = $beneficiarios->where('tipo_persona', 'beneficiario');
    @endphp

    {{-- Cotizantes --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Cotizantes</h3>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
            <table id="tabla-listado" class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">C.C.</th>
                        <th class="px-4 py-3">Nombres</th>
                        <th class="px-4 py-3">EPS</th>
                        <th class="px-4 py-3">Edad</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($cotizantes as $b)
                        <tr class="fila-dato hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $b->cc_afiliado }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $b->nombres }} {{ $b->apellidos }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->eps?->nombre_eps ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->edad }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->telefono }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->correo }}</td>
                            <td class="px-4 py-3">
                                @if ($b->usuario?->activo ?? true)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                    </span>
                                @endif
                                <form method="POST" action="{{ route('beneficiarios.estado', $b->cc_afiliado) }}" class="inline-block ml-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-teal-600 hover:underline">
                                        {{ ($b->usuario?->activo ?? true) ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('beneficiarios.edit', $b->cc_afiliado) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 text-xs font-medium hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="fila-vacio">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay cotizantes registrados.</td>
                        </tr>
                    @endforelse
                    <tr id="fila-vacio-busqueda" class="fila-vacio oculto">
                        <td colspan="7" class="px-4 py-12 text-center">
                            <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Beneficiarios --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Beneficiarios (familiares)</h3>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">C.C.</th>
                        <th class="px-4 py-3">Nombres</th>
                        <th class="px-4 py-3">Familiar de</th>
                        <th class="px-4 py-3">EPS</th>
                        <th class="px-4 py-3">Parentesco</th>
                        <th class="px-4 py-3">Edad</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($familiares as $b)
                        <tr class="hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $b->cc_afiliado }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $b->nombres }} {{ $b->apellidos }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $b->cotizante?->nombres }} {{ $b->cotizante?->apellidos }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->eps?->nombre_eps ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->parentesco ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->edad }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->telefono }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->correo }}</td>
                            <td class="px-4 py-3">
                                @if ($b->usuario?->activo ?? true)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                    </span>
                                @endif
                                <form method="POST" action="{{ route('beneficiarios.estado', $b->cc_afiliado) }}" class="inline-block ml-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-teal-600 hover:underline">
                                        {{ ($b->usuario?->activo ?? true) ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('beneficiarios.edit', $b->cc_afiliado) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 text-xs font-medium hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">No hay beneficiarios familiares registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    @include('admin.partials.script_listado')
@endpush