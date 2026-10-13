@extends('usuario.layout')

@section('titulo', 'Historia clínica')
@section('encabezado', 'Mi historia clínica')

@section('contenido')

    @php
        $bloques = [
            ['titulo' => 'Historias clínicas', 'icono' => 'historia',      'items' => $historias,      'tipo' => 'historia',      'color' => 'teal'],
            ['titulo' => 'Diagnósticos',       'icono' => 'lista',         'items' => $diagnosticos,   'tipo' => 'diagnostico',   'color' => 'indigo'],
            ['titulo' => 'Enfermedades',       'icono' => 'eps',           'items' => $enfermedades,   'tipo' => 'enfermedad',    'color' => 'rose'],
            ['titulo' => 'Procedimientos',     'icono' => 'procedimientos', 'items' => $procedimientos, 'tipo' => 'procedimiento', 'color' => 'amber'],
        ];
        $total = $historias->count() + $diagnosticos->count() + $enfermedades->count() + $procedimientos->count();
    @endphp

    <div class="mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Mi historia clínica</h2>
        <p class="text-sm text-gray-500">Diagnósticos, enfermedades y procedimientos registrados por tus médicos</p>
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <span class="text-2xl font-extrabold text-gray-900">{{ $total }}</span>
            <span class="block text-xs text-gray-500">Registros totales</span>
        </div>
        @foreach ($bloques as $bloque)
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <span class="text-2xl font-extrabold text-gray-900">{{ $bloque['items']->count() }}</span>
                <span class="block text-xs text-gray-500">{{ $bloque['titulo'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        @foreach ($bloques as $bloque)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                    <span class="w-9 h-9 rounded-lg bg-{{ $bloque['color'] }}-50 text-{{ $bloque['color'] }}-600 flex items-center justify-center">
                        @include('admin.partials.icono', ['icono' => $bloque['icono'], 'clase' => 'w-4 h-4'])
                    </span>
                    <h3 class="font-bold text-gray-900">{{ $bloque['titulo'] }}</h3>
                    <span class="ml-auto text-xs font-semibold bg-gray-100 text-gray-500 rounded-full px-2.5 py-0.5">{{ $bloque['items']->count() }}</span>
                </div>

                @forelse ($bloque['items'] as $item)
                    <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0 hover:border-{{ $bloque['color'] }}-200 transition">
                        @if ($bloque['tipo'] === 'historia')
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->fecha->format('d/m/Y') }}</span>
                                <span class="text-xs text-gray-400">Dr(a). {{ $item->medico->nombres ?? '' }} {{ $item->medico->apellidos ?? '' }}</span>
                            </div>
                            <div class="text-xs text-gray-600 space-y-1.5">
                                @if ($item->motivo_consulta)<p><span class="font-semibold text-gray-700">Motivo:</span> {{ $item->motivo_consulta }}</p>@endif
                                @if ($item->antecedentes)<p><span class="font-semibold text-gray-700">Antecedentes:</span> {{ $item->antecedentes }}</p>@endif
                                @if ($item->examen_fisico)<p><span class="font-semibold text-gray-700">Examen físico:</span> {{ $item->examen_fisico }}</p>@endif
                                @if ($item->observaciones)<p><span class="font-semibold text-gray-700">Observaciones:</span> {{ $item->observaciones }}</p>@endif
                            </div>
                        @elseif ($bloque['tipo'] === 'diagnostico')
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->descripcion }}</span>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $item->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($item->tipo)<span class="inline-block text-xs text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-full mt-2">{{ $item->tipo }}</span>@endif
                        @elseif ($bloque['tipo'] === 'enfermedad')
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->nombre }}</span>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $item->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($item->descripcion)<p class="text-xs text-gray-600 mt-1.5">{{ $item->descripcion }}</p>@endif
                        @else
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->descripcion }}</span>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $item->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($item->tipoProcedimiento)<span class="inline-block text-xs text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full mt-2">{{ $item->tipoProcedimiento->nombre }}</span>@endif
                            @if ($item->observaciones)<p class="text-xs text-gray-600 mt-1.5">{{ $item->observaciones }}</p>@endif
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Sin registros.</p>
                @endforelse
            </div>
        @endforeach
    </div>

@endsection
