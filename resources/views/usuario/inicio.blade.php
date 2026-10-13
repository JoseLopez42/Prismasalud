{{-- Inicio del panel del Usuario: bienvenida, próxima cita y accesos --}}
@extends('usuario.layout')

@section('titulo', 'Inicio')
@section('encabezado', 'Panel del Usuario')

@section('contenido')

    @php
        $iniciales = strtoupper(substr($usuario->nombres, 0, 1) . substr($usuario->apellidos, 0, 1));
    @endphp

    {{-- Bienvenida --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 md:p-8 mb-6">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center text-xl font-bold flex-shrink-0">
                {{ $iniciales }}
            </div>
            <div class="flex-1">
                <p class="text-white/70 text-sm">{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                <h2 class="text-xl md:text-2xl font-bold mt-0.5">Hola, {{ $usuario->nombres }} {{ $usuario->apellidos }}</h2>
                <p class="text-white/80 text-sm mt-1">Bienvenido a tu espacio de salud en PrismaSalud IPS.</p>
            </div>
            <a href="{{ route('usuario.citas.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-[#1A5C3A] text-sm font-bold rounded-lg hover:bg-teal-50 shadow-md transition flex-shrink-0">
                @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
                Agendar cita
            </a>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-2 top-16 w-24 h-24 rounded-full bg-white/5"></div>
    </div>

    {{-- Próxima cita --}}
    @if ($proximaCita)
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-6 mb-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-base font-bold text-gray-900">Tu próxima cita</h3>
                <a href="{{ route('usuario.citas.index') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-800">Ver todas</a>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 flex flex-col items-center justify-center flex-shrink-0">
                    <span class="text-lg font-extrabold text-teal-700 leading-none">{{ $proximaCita->fecha->format('d') }}</span>
                    <span class="text-[11px] font-semibold text-teal-600 uppercase">{{ $proximaCita->fecha->locale('es')->translatedFormat('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            {{ $proximaCita->estadoCita->nombre ?? 'Pendiente' }}
                        </span>
                        <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full">
                            {{ $proximaCita->tipoCita->nombre ?? 'Consulta' }}
                        </span>
                    </div>
                    <p class="font-semibold text-gray-900 truncate">
                        Dr(a). {{ $proximaCita->medico->nombres ?? '' }} {{ $proximaCita->medico->apellidos ?? '' }}
                        <span class="font-normal text-gray-500">· {{ $proximaCita->medico->especialidad->nombre ?? '' }}</span>
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ substr($proximaCita->hora_inicio, 0, 5) }} – {{ substr($proximaCita->hora_fin, 0, 5) }}
                        @if ($proximaCita->consultorio) · {{ $proximaCita->consultorio->nombre }} @endif
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-6 mb-6 text-center">
            <p class="text-sm font-semibold text-gray-700">No tienes citas programadas</p>
            <p class="text-sm text-gray-400 mt-1">Agenda tu próxima cita médica cuando lo necesites.</p>
            <a href="{{ route('usuario.citas.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
                Agendar cita
            </a>
        </div>
    @endif

    {{-- Resumen --}}
    <h3 class="text-base font-bold text-gray-900 mb-3">Resumen</h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach ($resumen as $item)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 mb-3">
                    @include('admin.partials.icono', ['icono' => $item['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $item['numero'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $item['texto'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Servicios --}}
    <h3 class="text-base font-bold text-gray-900 mb-1">Servicios disponibles</h3>
    <p class="text-sm text-gray-500 mb-4">Acceso rápido a los servicios que puedes gestionar desde tu cuenta</p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($servicios as $servicio)
            <a href="{{ $servicio['ruta'] }}"
               class="group bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-md hover:border-teal-200 hover:-translate-y-0.5 transition block">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 flex-shrink-0 group-hover:scale-110 transition-transform">
                        @include('admin.partials.icono', ['icono' => $servicio['icono'], 'clase' => 'w-5 h-5'])
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $servicio['titulo'] }}</p>
                </div>
                <p class="text-xs text-gray-500 mb-4">{{ $servicio['descripcion'] }}</p>
                <span class="inline-flex items-center gap-1 text-xs font-medium text-teal-700 bg-teal-50 border border-teal-100 px-3 py-1.5 rounded-full group-hover:bg-teal-600 group-hover:text-white group-hover:border-teal-600 transition">
                    Consultar
                    @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-3 h-3'])
                </span>
            </a>
        @endforeach
    </div>

@endsection
