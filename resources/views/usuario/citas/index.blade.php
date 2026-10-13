@extends('usuario.layout')

@section('titulo', 'Mis citas')
@section('encabezado', 'Mis citas')

@section('contenido')

    @php
        $hoy = now()->toDateString();
        $proximas = $citas->filter(fn ($c) => $c->activo && $c->fecha->toDateString() >= $hoy)->sortBy(fn ($c) => $c->fecha->toDateString() . ' ' . $c->hora_inicio);
        $anteriores = $citas->reject(fn ($c) => $c->activo && $c->fecha->toDateString() >= $hoy);

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

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Mis citas</h2>
            <p class="text-sm text-gray-500">Consulta y gestiona tus citas médicas programadas</p>
        </div>
        <a href="{{ route('usuario.citas.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
            @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
            Agendar cita
        </a>
    </div>

    @if (session('exito'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 text-sm rounded-xl px-4 py-3 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('exito') }}
        </div>
    @endif

    {{-- Próximas --}}
    <h3 class="text-base font-bold text-gray-900 mb-3">Próximas citas</h3>
    <div class="grid md:grid-cols-2 gap-4 mb-8">
        @forelse ($proximas as $cita)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex">
                <div class="w-1.5 bg-teal-500 flex-shrink-0"></div>
                <div class="flex-1 p-5">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-teal-50 border border-teal-100 flex flex-col items-center justify-center flex-shrink-0">
                                <span class="text-base font-extrabold text-teal-700 leading-none">{{ $cita->fecha->format('d') }}</span>
                                <span class="text-[10px] font-semibold text-teal-600 uppercase">{{ $cita->fecha->locale('es')->translatedFormat('M') }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $cita->tipoCita->nombre ?? 'Consulta' }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $cita->dia_semana }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border {{ $colorEstado($cita->estadoCita->nombre ?? null) }}">
                            {{ $cita->estadoCita->nombre ?? 'Pendiente' }}
                        </span>
                    </div>

                    <dl class="text-sm space-y-1.5">
                        <div class="flex items-center gap-2 text-gray-700">
                            @include('admin.partials.icono', ['icono' => 'medicos', 'clase' => 'w-4 h-4 text-gray-400'])
                            <span class="font-medium">
                                {{ $cita->medico ? 'Dr(a). ' . $cita->medico->nombres . ' ' . $cita->medico->apellidos : 'Sin asignar' }}
                            </span>
                            <span class="text-gray-400 text-xs">{{ $cita->medico->especialidad->nombre ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4 text-gray-400'])
                            <span>{{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            @include('admin.partials.icono', ['icono' => 'ubicaciones', 'clase' => 'w-4 h-4 text-gray-400'])
                            <span>{{ $cita->consultorio->nombre ?? 'Consultorio por asignar' }}</span>
                        </div>
                    </dl>

                    @if ($cita->motivo)
                        <p class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">Motivo: {{ $cita->motivo }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-white border border-dashed border-gray-300 rounded-2xl p-10 text-center">
                <div class="inline-flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                        @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-8 h-8 text-teal-400'])
                    </div>
                    <p class="text-base font-semibold text-gray-700">No tienes citas programadas</p>
                    <p class="text-sm text-gray-400 mt-1">Agenda tu primera cita y quedará asignada a un médico.</p>
                    <a href="{{ route('usuario.citas.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                        @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
                        Agendar cita
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Historial --}}
    @if ($anteriores->isNotEmpty())
        <h3 class="text-base font-bold text-gray-900 mb-3">Historial de citas</h3>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm divide-y divide-gray-100">
            @foreach ($anteriores as $cita)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-gray-600 leading-none">{{ $cita->fecha->format('d') }}</span>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $cita->fecha->locale('es')->translatedFormat('M') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $cita->tipoCita->nombre ?? 'Consulta' }}
                            <span class="font-normal text-gray-500 text-xs">· {{ substr($cita->hora_inicio, 0, 5) }}</span>
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $cita->medico ? 'Dr(a). ' . $cita->medico->nombres . ' ' . $cita->medico->apellidos : 'Sin asignar' }}
                            @if ($cita->medico->especialidad->nombre ?? null) · {{ $cita->medico->especialidad->nombre }} @endif
                        </p>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border flex-shrink-0 {{ $colorEstado($cita->estadoCita->nombre ?? null) }}">
                        {{ $cita->estadoCita->nombre ?? 'Pendiente' }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

@endsection
