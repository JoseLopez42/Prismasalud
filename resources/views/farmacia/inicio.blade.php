@extends('farmacia.layout')

@section('titulo', 'Inicio')
@section('encabezado', 'Panel de Farmacia')

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

    @php
        $nombre = $_SESSION['usuario_nombres'] ?? 'Farmacéutico';
        $partes = preg_split('/\s+/', trim($nombre));
        $iniciales = strtoupper(substr($partes[0] ?? 'F', 0, 1) . substr($partes[1] ?? '', 0, 1));
        $hora = (int) now()->format('G');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    @endphp

    {{-- Bienvenida --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 md:p-8 mb-6">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center text-xl font-bold flex-shrink-0">
                {{ $iniciales }}
            </div>
            <div class="flex-1">
                <p class="text-white/70 text-sm">{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                <h2 class="text-xl md:text-2xl font-bold mt-0.5">{{ $saludo }}, {{ $nombre }}</h2>
                <p class="text-white/80 text-sm mt-1">Entrega de medicamentos y control del inventario de la farmacia</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('farmacia.prescripciones.index', ['estado' => 'pendiente']) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#1A5C3A] text-sm font-bold rounded-lg hover:bg-teal-50 shadow-md transition">
                    @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-4 h-4'])
                    Entregar medicamentos
                </a>
                <a href="{{ route('farmacia.inventario.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 ring-1 ring-white/25 text-white text-sm font-semibold rounded-lg hover:bg-white/20 transition">
                    @include('admin.partials.icono', ['icono' => 'inventario', 'clase' => 'w-4 h-4'])
                    Inventario
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-2 top-16 w-24 h-24 rounded-full bg-white/5"></div>
    </div>

    {{-- Resumen --}}
    @php
        $acentos = [
            ['barra' => 'bg-gradient-to-r from-amber-500 to-amber-300',   'icono' => 'bg-amber-50 text-amber-600'],
            ['barra' => 'bg-gradient-to-r from-teal-500 to-teal-300',     'icono' => 'bg-teal-50 text-teal-600'],
            ['barra' => 'bg-gradient-to-r from-blue-500 to-sky-300',      'icono' => 'bg-blue-50 text-blue-600'],
            ['barra' => 'bg-gradient-to-r from-rose-500 to-rose-300',     'icono' => 'bg-rose-50 text-rose-600'],
        ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
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

    {{-- Prescripciones pendientes --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-900">Prescripciones pendientes</h3>
            <p class="text-sm text-gray-500">Medicamentos formulados que aún no se han entregado</p>
        </div>
        <a href="{{ route('farmacia.prescripciones.index', ['estado' => 'pendiente']) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-700 hover:text-teal-800">
            Ver todas
            @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-4 h-4'])
        </a>
    </div>

    @if ($pendientes->count())
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm divide-y divide-gray-100 mb-10">
            @foreach ($pendientes as $prescripcion)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center flex-shrink-0">
                        @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-5 h-5 text-amber-600'])
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $prescripcion->paciente ? $prescripcion->paciente->nombres . ' ' . $prescripcion->paciente->apellidos : 'Paciente no encontrado' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $prescripcion->medicamento }}
                            @if ($prescripcion->id_medicamento) · <span class="font-mono">{{ $prescripcion->id_medicamento }}</span> @endif
                             · {{ $prescripcion->fecha->format('d/m/Y') }}
                             @if ($prescripcion->fecha_limite)
                                 · Límite: {{ $prescripcion->fecha_limite->format('d/m/Y') }}
                             @endif
                         </p>
                    </div>
                    <a href="{{ route('farmacia.prescripciones.detalle', $prescripcion->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition flex-shrink-0">
                        @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-4 h-4'])
                        Entregar
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center mb-10">
            <div class="inline-flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                    @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-10 h-10 text-teal-400'])
                </div>
                <p class="text-base font-semibold text-gray-700">No hay prescripciones pendientes</p>
                <p class="text-sm text-gray-400 mt-1">Cuando un médico formule un medicamento, aparecerá aquí para entregarlo.</p>
            </div>
        </div>
    @endif

    {{-- Últimas entregas --}}
    @if ($ultimasEntregas->count())
        <h3 class="text-base font-bold text-gray-900 mb-1">Últimas entregas</h3>
        <p class="text-sm text-gray-500 mb-4">Entregas registradas recientemente</p>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm divide-y divide-gray-100">
            @foreach ($ultimasEntregas as $entrega)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center flex-shrink-0">
                        @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-5 h-5 text-teal-600'])
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $entrega->paciente ? $entrega->paciente->nombres . ' ' . $entrega->paciente->apellidos : 'Paciente no encontrado' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $entrega->medicamento }}
                            · {{ $entrega->cantidad_entregada ?? 1 }} {{ ($entrega->cantidad_entregada ?? 1) === 1 ? 'unidad' : 'unidades' }}
                        </p>
                    </div>
                    <span class="hidden sm:inline text-[11px] font-semibold text-gray-500 bg-gray-100 rounded-full px-2.5 py-1 flex-shrink-0">
                        {{ $entrega->fecha_entrega ? $entrega->fecha_entrega->format('d/m/Y H:i') : '—' }}
                    </span>
                    <a href="{{ route('farmacia.prescripciones.detalle', $entrega->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 border border-teal-100 text-xs font-semibold hover:bg-teal-600 hover:text-white transition flex-shrink-0">
                        Ver
                    </a>
                </div>
            @endforeach
        </div>
    @endif

@endsection
