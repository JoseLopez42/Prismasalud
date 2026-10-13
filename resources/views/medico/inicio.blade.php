@extends('medico.layout')

@section('titulo', 'Inicio')
@section('encabezado', 'Panel del Médico')

@section('contenido')

    @php
        $hora = (int) now()->format('G');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
        $iniciales = strtoupper(substr($medico->nombres ?? 'M', 0, 1) . substr($medico->apellidos ?? '', 0, 1));

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
    @endphp

    {{-- Bienvenida --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 md:p-8 mb-6">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center text-xl font-bold flex-shrink-0">
                {{ $iniciales }}
            </div>
            <div class="flex-1">
                <p class="text-white/70 text-sm">{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                <h2 class="text-xl md:text-2xl font-bold mt-0.5">{{ $saludo }}, Dr(a). {{ $medico->nombres ?? '' }} {{ $medico->apellidos ?? '' }}</h2>
                <p class="text-white/80 text-sm mt-1">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }} · {{ $citasHoy }} {{ $citasHoy === 1 ? 'cita' : 'citas' }} para hoy</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('medico.historia.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#1A5C3A] text-sm font-bold rounded-lg hover:bg-teal-50 shadow-md transition">
                    @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                    Buscar historia
                </a>
                <a href="{{ route('medico.procedimiento.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 ring-1 ring-white/25 text-white text-sm font-semibold rounded-lg hover:bg-white/20 transition">
                    @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
                    Procedimientos
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-2 top-16 w-24 h-24 rounded-full bg-white/5"></div>
    </div>

    {{-- Resumen --}}
    @php
        $acentos = [
            ['barra' => 'bg-gradient-to-r from-teal-500 to-teal-300',   'icono' => 'bg-teal-50 text-teal-600'],
            ['barra' => 'bg-gradient-to-r from-blue-500 to-sky-300',     'icono' => 'bg-blue-50 text-blue-600'],
            ['barra' => 'bg-gradient-to-r from-violet-500 to-violet-300','icono' => 'bg-violet-50 text-violet-600'],
        ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        @foreach ($resumen as $item)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 relative overflow-hidden hover:shadow-md transition">
                <span class="absolute inset-x-0 top-0 h-1 {{ $acentos[$loop->index % count($acentos)]['barra'] }}"></span>
                <div class="w-10 h-10 rounded-lg {{ $acentos[$loop->index % count($acentos)]['icono'] }} flex items-center justify-center mb-3">
                    @include('admin.partials.icono', ['icono' => $item['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ $item['numero'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['texto'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Próximas citas --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-900">Próximas citas</h3>
            <p class="text-sm text-gray-500">Revisa al paciente y atiende la cita desde aquí</p>
        </div>
    </div>

    @if ($citasProximas->count())
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4 mb-10">
            @foreach ($citasProximas as $cita)
                @php $esHoy = $cita->fecha->isToday(); @endphp
                <div class="bg-white border rounded-2xl shadow-sm overflow-hidden flex {{ $esHoy ? 'border-teal-300 ring-1 ring-teal-100' : 'border-gray-200' }}">
                    <div class="w-1.5 {{ $esHoy ? 'bg-teal-500' : 'bg-gray-200' }} flex-shrink-0"></div>
                    <div class="flex-1 p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl bg-teal-50 border border-teal-100 flex flex-col items-center justify-center flex-shrink-0">
                                    <span class="text-base font-extrabold text-teal-700 leading-none">{{ $cita->fecha->format('d') }}</span>
                                    <span class="text-[10px] font-semibold text-teal-600 uppercase">{{ $cita->fecha->locale('es')->translatedFormat('M') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">
                                        {{ $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Paciente no encontrado' }}
                                    </p>
                                    <p class="text-xs text-gray-400">C.C. {{ $cita->cedula_usuario }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border flex-shrink-0 {{ $colorEstado($cita->estadoCita->nombre ?? null) }}">
                                {{ $cita->estadoCita->nombre ?? 'Pendiente' }}
                            </span>
                        </div>

                        <dl class="text-sm space-y-1.5 mb-4">
                            <div class="flex items-center gap-2 text-gray-700">
                                @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4 text-gray-400'])
                                <span>{{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}</span>
                                @if ($esHoy)
                                    <span class="ml-auto text-[11px] font-bold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-full">Hoy</span>
                                @else
                                    <span class="ml-auto text-[11px] text-gray-400 capitalize">{{ $cita->dia_semana }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                @include('admin.partials.icono', ['icono' => 'ubicaciones', 'clase' => 'w-4 h-4 text-gray-400'])
                                <span>{{ $cita->consultorio->nombre ?? 'Sin consultorio' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                @include('admin.partials.icono', ['icono' => 'lista', 'clase' => 'w-4 h-4 text-gray-400'])
                                <span>{{ $cita->tipoCita->nombre ?? 'Consulta' }}</span>
                            </div>
                        </dl>

                        <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('medico.cita.show', $cita->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white border border-green-600 text-xs font-semibold hover:bg-green-700 transition">
                                @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                                Atender cita
                            </a>
                            <a href="{{ route('medico.procedimiento.index', ['cedula' => $cita->cedula_usuario, 'cita' => $cita->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold hover:bg-indigo-600 hover:text-white transition">
                                @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
                                Historial
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center mb-10">
            <div class="inline-flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-10 h-10 text-teal-400'])
                </div>
                <p class="text-base font-semibold text-gray-700">No tienes citas próximas</p>
                <p class="text-sm text-gray-400 mt-1">Cuando un paciente agende contigo, aparecerá aquí.</p>
            </div>
        </div>
    @endif

    {{-- Citas anteriores --}}
    @if ($citasAnteriores->count())
        <h3 class="text-base font-bold text-gray-900 mb-1">Citas anteriores</h3>
        <p class="text-sm text-gray-500 mb-4">Historial de citas ya pasadas (últimas 10)</p>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm divide-y divide-gray-100">
            @foreach ($citasAnteriores as $cita)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-gray-600 leading-none">{{ $cita->fecha->format('d') }}</span>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $cita->fecha->locale('es')->translatedFormat('M') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Paciente no encontrado' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
                            · {{ $cita->tipoCita->nombre ?? 'Consulta' }}
                            @if ($cita->consultorio) · {{ $cita->consultorio->nombre }} @endif
                        </p>
                    </div>
                    <span class="hidden sm:inline-flex text-[11px] font-semibold px-2.5 py-1 rounded-full border flex-shrink-0 {{ $colorEstado($cita->estadoCita->nombre ?? null) }}">
                        {{ $cita->estadoCita->nombre ?? '—' }}
                    </span>
                    <a href="{{ route('medico.cita.show', $cita->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 border border-teal-100 text-xs font-semibold hover:bg-teal-600 hover:text-white transition flex-shrink-0">
                        Ver
                    </a>
                </div>
            @endforeach
        </div>
    @endif

@endsection
