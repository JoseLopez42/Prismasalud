@extends('medico.layout')

@section('titulo', 'Detalle del horario')
@section('encabezado', 'Detalle del horario')

@section('contenido')

    @php
        $colorEstado = function ($nombre) {
            $n = strtolower($nombre ?? 'pendiente');
            return match (true) {
                str_contains($n, 'confirm') => 'text-green-700 bg-green-50 border-green-100',
                str_contains($n, 'atend') || str_contains($n, 'complet') => 'text-teal-700 bg-teal-50 border-teal-100',
                str_contains($n, 'cancel') => 'text-red-700 bg-red-50 border-red-100',
                str_contains($n, 'pend') => 'text-amber-700 bg-amber-50 border-amber-100',
                default => 'text-gray-700 bg-gray-100 border-gray-200',
            };
        };
        $esHoy = $horario->fecha->isToday();
    @endphp

    {{-- Volver --}}
    <a href="{{ route('medico.horario.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-700 hover:text-teal-900 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        Volver a mis horarios
    </a>

    {{-- Ficha del horario --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 mb-6">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/15 ring-1 ring-white/25 flex flex-col items-center justify-center flex-shrink-0">
                <span class="text-xl font-extrabold leading-none">{{ $horario->fecha->format('d') }}</span>
                <span class="text-[10px] font-semibold uppercase text-white/80">{{ $horario->fecha->locale('es')->translatedFormat('M') }}</span>
            </div>
            <div class="flex-1">
                <p class="text-white/70 text-sm capitalize">{{ $horario->dia_semana }}, {{ $horario->fecha->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</p>
                <h2 class="text-xl md:text-2xl font-bold mt-0.5">{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</h2>
                <p class="text-white/80 text-sm mt-1 flex items-center gap-2">
                    @include('admin.partials.icono', ['icono' => 'ubicaciones', 'clase' => 'w-4 h-4'])
                    {{ $horario->consultorio->nombre ?? 'Sin consultorio' }}
                </p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-3xl font-extrabold tabular-nums">{{ $pacientes->count() }}</p>
                <p class="text-white/80 text-sm">{{ $pacientes->count() === 1 ? 'paciente agendado' : 'pacientes agendados' }}</p>
            </div>
        </div>
        @if ($esHoy)
            <span class="absolute top-4 right-4 text-[11px] font-bold text-teal-700 bg-white px-2.5 py-1 rounded-full">Hoy</span>
        @endif
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
    </div>

    {{-- Pacientes del horario --}}
    <h3 class="text-base font-bold text-gray-900 mb-1">Pacientes de este horario</h3>
    <p class="text-sm text-gray-500 mb-4">Citas programadas dentro del bloque {{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</p>

    @if ($pacientes->count())
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm divide-y divide-gray-100">
            @foreach ($pacientes as $cita)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center flex-shrink-0 text-teal-700">
                        @include('admin.partials.icono', ['icono' => 'usuarios', 'clase' => 'w-5 h-5'])
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Paciente no encontrado' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            C.C. {{ $cita->cedula_usuario }}
                            · {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
                            · {{ $cita->tipoCita->nombre ?? 'Consulta' }}
                        </p>
                    </div>
                    <span class="hidden sm:inline-flex text-[11px] font-semibold px-2.5 py-1 rounded-full border flex-shrink-0 {{ $colorEstado($cita->estadoCita->nombre ?? null) }}">
                        {{ $cita->estadoCita->nombre ?? 'Pendiente' }}
                    </span>
                    <a href="{{ route('medico.cita.show', $cita->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white border border-green-600 text-xs font-semibold hover:bg-green-700 transition flex-shrink-0">
                        @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                        Atender
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
            <div class="inline-flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                    @include('admin.partials.icono', ['icono' => 'usuarios', 'clase' => 'w-10 h-10 text-gray-300'])
                </div>
                <p class="text-base font-semibold text-gray-700">Sin pacientes en este horario</p>
                <p class="text-sm text-gray-400 mt-1">Ningún paciente ha agendado una cita en este bloque de tiempo.</p>
            </div>
        </div>
    @endif

@endsection
