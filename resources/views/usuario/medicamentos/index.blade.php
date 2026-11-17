@extends('usuario.layout')

@section('titulo', 'Mis medicamentos')
@section('encabezado', 'Mis medicamentos')

@section('contenido')

    <div class="mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Mis medicamentos</h2>
        <p class="text-sm text-gray-500">Prescripciones que los médicos te han formulado durante tus citas</p>
    </div>

    @if ($prescripciones->isNotEmpty())
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($prescripciones as $prescripcion)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 flex flex-col">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                            @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-5 h-5'])
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 leading-snug">{{ $prescripcion->medicamento }}</p>
                            @if ($prescripcion->id_medicamento)
                                <p class="text-[11px] text-gray-400 font-mono">{{ $prescripcion->id_medicamento }}</p>
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold text-gray-500 bg-gray-100 rounded-full px-2.5 py-1 flex-shrink-0">
                            {{ $prescripcion->fecha->format('d/m/Y') }}
                        </span>
                    </div>

                    <div class="mb-4">
                        @if ($prescripcion->estado === 'entregada')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-100 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Entregado {{ $prescripcion->fecha_entrega ? 'el ' . $prescripcion->fecha_entrega->format('d/m/Y') : '' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Pendiente de entrega en farmacia
                            </span>
                        @endif
                        @if ($prescripcion->fecha_limite && $prescripcion->estado !== 'entregada')
                            <p class="text-xs {{ $prescripcion->vencida() ? 'text-rose-600 font-semibold' : 'text-gray-500' }} mt-1.5">
                                {{ $prescripcion->vencida() ? 'Plazo vencido el' : 'Reclamar antes del' }} {{ $prescripcion->fecha_limite->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @if ($prescripcion->dosis)
                            <span class="text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-full">Dosis: {{ $prescripcion->dosis }}</span>
                        @endif
                        @if ($prescripcion->frecuencia)
                            <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full">{{ $prescripcion->frecuencia }}</span>
                        @endif
                        @if ($prescripcion->duracion)
                            <span class="text-xs font-medium text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-full">Duración: {{ $prescripcion->duracion }}</span>
                        @endif
                    </div>

                    @if ($prescripcion->indicaciones)
                        <p class="text-xs text-gray-600 bg-gray-50 rounded-lg px-3 py-2 mb-4">{{ $prescripcion->indicaciones }}</p>
                    @endif

                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                        @include('admin.partials.icono', ['icono' => 'medicos', 'clase' => 'w-4 h-4 text-gray-400'])
                        {{ $prescripcion->medico ? 'Dr(a). ' . $prescripcion->medico->nombres . ' ' . $prescripcion->medico->apellidos : 'Médico no disponible' }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
            <div class="inline-flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                    @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-10 h-10 text-teal-400'])
                </div>
                <p class="text-base font-semibold text-gray-700">Aún no tienes medicamentos formulados</p>
                <p class="text-sm text-gray-400 mt-1">Cuando un médico te formule un medicamento, aparecerá aquí.</p>
            </div>
        </div>
    @endif

@endsection
