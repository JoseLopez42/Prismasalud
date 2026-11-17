@extends('farmacia.layout')

@section('titulo', 'Prescripciones')
@section('encabezado', 'Farmacia · Prescripciones')

@section('contenido')

    @if (session('exito'))
        <div class="flex items-center gap-2 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3 mb-5 border border-green-100">
            @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-5 h-5 flex-shrink-0'])
            {{ session('exito') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3 mb-5 border border-red-100">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Prescripciones</h2>
            <p class="text-sm text-gray-500">Medicamentos formulados por los médicos a los pacientes</p>
        </div>
    </div>

    {{-- Filtros por estado --}}
    @php
        $filtros = [
            'pendiente' => 'Pendientes',
            'entregada' => 'Entregadas',
            'todas'     => 'Todas',
        ];
    @endphp
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach ($filtros as $clave => $texto)
            <a href="{{ route('farmacia.prescripciones.index', array_filter(['estado' => $clave, 'buscar' => $buscar ?: null])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition
                    {{ $estado === $clave ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                {{ $texto }}
            </a>
        @endforeach
    </div>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('farmacia.prescripciones.index') }}" class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="hidden" name="estado" value="{{ $estado }}">
        <div class="relative sm:max-w-md w-full">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por cédula, nombre del paciente o medicamento..."
                   class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
        </div>
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition">
            Buscar
        </button>
        @if ($buscar !== '')
            <a href="{{ route('farmacia.prescripciones.index', ['estado' => $estado]) }}"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                Limpiar
            </a>
        @endif
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Paciente</th>
                        <th class="px-4 py-3">Medicamento</th>
                        <th class="px-4 py-3">Médico</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($prescripciones as $prescripcion)
                        @php
                            $esEntregada = $prescripcion->estado === 'entregada';
                            $stock = $prescripcion->medicamentoCatalogo?->stock;
                        @endphp
                        <tr class="hover:bg-teal-50/40 transition">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">
                                    {{ $prescripcion->paciente ? $prescripcion->paciente->nombres . ' ' . $prescripcion->paciente->apellidos : 'Paciente no encontrado' }}
                                </p>
                                <p class="text-xs text-gray-400">C.C. {{ $prescripcion->cedula_paciente }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $prescripcion->medicamento }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if ($prescripcion->id_medicamento)
                                        <span class="font-mono text-[11px] text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">{{ $prescripcion->id_medicamento }}</span>
                                    @endif
                                    @if ($stock !== null)
                                        <span class="text-[11px] {{ $stock > 0 ? 'text-gray-500' : 'text-rose-600 font-semibold' }}">Stock: {{ $stock }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $prescripcion->medico ? 'Dr(a). ' . $prescripcion->medico->nombres . ' ' . $prescripcion->medico->apellidos : '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $prescripcion->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @if ($esEntregada)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Entregada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('farmacia.prescripciones.detalle', $prescripcion->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition
                                            {{ $esEntregada ? 'bg-teal-50 text-teal-700 border-teal-100 hover:bg-teal-600 hover:text-white' : 'bg-green-600 text-white border-green-600 hover:bg-green-700' }}">
                                        @include('admin.partials.icono', ['icono' => $esEntregada ? 'lista' : 'entrega', 'clase' => 'w-4 h-4'])
                                        {{ $esEntregada ? 'Ver' : 'Entregar' }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                                        @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-10 h-10 text-teal-400'])
                                    </div>
                                    <p class="text-base font-semibold text-gray-700">No hay prescripciones</p>
                                    <p class="text-sm text-gray-400 mt-1">No se encontraron prescripciones con ese filtro o búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($prescripciones->hasPages())
        <div class="mt-5">
            {{ $prescripciones->links() }}
        </div>
    @endif

@endsection
