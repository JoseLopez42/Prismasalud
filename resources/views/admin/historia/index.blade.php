@extends('admin.layout')

@section('titulo', 'Historia clínica')
@section('encabezado', 'Historia Clínica')

@section('contenido')

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900">Historias médicas</h2>
        <p class="text-sm text-gray-500">Todas las historias clínicas registradas por los médicos, con sus diagnósticos y enfermedades</p>
    </div>

    {{-- Buscador por cédula del paciente --}}
    <form method="GET" action="{{ route('historia.index') }}"
          class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Filtrar por cédula del paciente</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="number" name="cedula" value="{{ $buscar }}"
                       placeholder="Ej: 1065581109" min="1"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
            </div>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                Buscar
            </button>
            @if ($buscar !== '')
                <a href="{{ route('historia.index') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                    Limpiar
                </a>
            @endif
        </div>
    </form>

    <div class="grid gap-4">
        @forelse ($historias as $historia)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                {{-- Cabecera: paciente y médico --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-teal-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($historia->paciente->nombres ?? '?', 0, 1) . substr($historia->paciente->apellidos ?? '', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 truncate">
                                {{ $historia->paciente ? $historia->paciente->nombres . ' ' . $historia->paciente->apellidos : 'Paciente no encontrado' }}
                            </p>
                            <p class="text-xs text-gray-500">C.C. {{ $historia->cedula_paciente }}</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-sm font-semibold text-gray-700">Dr(a). {{ $historia->medico->nombres ?? '' }} {{ $historia->medico->apellidos ?? '' }}</p>
                        <p class="text-xs text-gray-400">{{ $historia->medico->especialidad->nombre ?? '' }}</p>
                        <p class="text-xs text-gray-400">Fecha: {{ $historia->fecha->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Cuerpo: contenido clínico --}}
                <div class="px-5 py-4 grid sm:grid-cols-2 gap-4 text-sm">
                    @if ($historia->motivo_consulta)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-0.5">Motivo de consulta</p>
                            <p class="text-gray-700">{{ $historia->motivo_consulta }}</p>
                        </div>
                    @endif
                    @if ($historia->antecedentes)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-0.5">Antecedentes</p>
                            <p class="text-gray-700">{{ $historia->antecedentes }}</p>
                        </div>
                    @endif
                    @if ($historia->examen_fisico)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-0.5">Examen físico</p>
                            <p class="text-gray-700">{{ $historia->examen_fisico }}</p>
                        </div>
                    @endif
                    @if ($historia->observaciones)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-0.5">Observaciones</p>
                            <p class="text-gray-700">{{ $historia->observaciones }}</p>
                        </div>
                    @endif
                </div>

                {{-- Diagnósticos y enfermedades --}}
                <div class="px-5 pb-4 grid sm:grid-cols-2 gap-4">
                    <div class="border border-gray-100 rounded-xl p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Diagnósticos ({{ $historia->diagnosticos->count() }})</p>
                        @forelse ($historia->diagnosticos as $diagnostico)
                            <p class="text-sm text-gray-700 mb-1">
                                · {{ $diagnostico->descripcion }}
                                @if ($diagnostico->tipo)
                                    <span class="text-xs text-teal-700 bg-teal-50 border border-teal-100 px-1.5 py-0.5 rounded-full">{{ $diagnostico->tipo }}</span>
                                @endif
                            </p>
                        @empty
                            <p class="text-xs text-gray-400">Sin diagnósticos.</p>
                        @endforelse
                    </div>
                    <div class="border border-gray-100 rounded-xl p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Enfermedades ({{ $historia->enfermedades->count() }})</p>
                        @forelse ($historia->enfermedades as $enfermedad)
                            <p class="text-sm text-gray-700 mb-1">· {{ $enfermedad->nombre }}</p>
                        @empty
                            <p class="text-xs text-gray-400">Sin enfermedades.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-4 py-16 text-center">
                <div class="inline-flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                        @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-10 h-10 text-teal-400'])
                    </div>
                    <p class="text-base font-semibold text-gray-700">
                        {{ $buscar !== '' ? 'No hay historias para esa cédula' : 'Todavía no hay historias médicas' }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">Las historias aparecen aquí cuando los médicos las registran.</p>
                </div>
            </div>
        @endforelse
    </div>

@endsection
