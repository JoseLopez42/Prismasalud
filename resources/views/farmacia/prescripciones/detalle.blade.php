@extends('farmacia.layout')

@section('titulo', 'Detalle de prescripción')
@section('encabezado', 'Farmacia · Detalle de prescripción')

@section('contenido')

    @php
        $esEntregada = $prescripcion->estado === 'entregada';
        $stock = $prescripcion->medicamentoCatalogo?->stock;
    @endphp

    <div class="mb-5">
        <a href="{{ route('farmacia.prescripciones.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-700 hover:text-teal-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver al listado
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3 mb-5 border border-red-100">
            <p class="font-semibold mb-1">No se pudo registrar la entrega:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Medicamento formulado --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                        @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-6 h-6'])
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-lg font-bold text-gray-900">{{ $prescripcion->medicamento }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            @if ($prescripcion->id_medicamento)
                                <span class="font-mono text-[11px] text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">{{ $prescripcion->id_medicamento }}</span>
                            @endif
                            <span class="text-[11px] text-gray-500">Formulada el {{ $prescripcion->fecha->format('d/m/Y') }}</span>
                            @if ($prescripcion->fecha_limite)
                                <span class="text-[11px] font-semibold {{ $prescripcion->vencida() ? 'text-rose-600 bg-rose-50' : 'text-amber-700 bg-amber-50' }} px-2 py-0.5 rounded-full">
                                    Reclamar antes del {{ $prescripcion->fecha_limite->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if ($esEntregada)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-3 py-1.5 rounded-full flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Entregada
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-full flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pendiente
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2 mb-5">
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
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Indicaciones</p>
                        <p class="text-sm text-gray-700">{{ $prescripcion->indicaciones }}</p>
                    </div>
                @endif
            </div>

            {{-- Paciente y médico --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        @include('admin.partials.icono', ['icono' => 'perfil', 'clase' => 'w-4 h-4 text-gray-400'])
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Paciente</p>
                    </div>
                    <p class="font-semibold text-gray-900">
                        {{ $prescripcion->paciente ? $prescripcion->paciente->nombres . ' ' . $prescripcion->paciente->apellidos : 'Paciente no encontrado' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-0.5">C.C. {{ $prescripcion->cedula_paciente }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        @include('admin.partials.icono', ['icono' => 'medicos', 'clase' => 'w-4 h-4 text-gray-400'])
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Médico</p>
                    </div>
                    <p class="font-semibold text-gray-900">
                        {{ $prescripcion->medico ? 'Dr(a). ' . $prescripcion->medico->nombres . ' ' . $prescripcion->medico->apellidos : 'No disponible' }}
                    </p>
                    @if ($prescripcion->medico && $prescripcion->medico->especialidad)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $prescripcion->medico->especialidad->nombre }}</p>
                    @endif
                </div>
            </div>

            {{-- Entrega ya registrada --}}
            @if ($esEntregada)
                <div class="bg-white border border-green-200 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center gap-2 mb-4">
                        @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-5 h-5 text-green-600'])
                        <h3 class="text-base font-bold text-gray-900">Entrega registrada</h3>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Fecha de entrega</dt>
                            <dd class="font-semibold text-gray-900">{{ $prescripcion->fecha_entrega ? $prescripcion->fecha_entrega->format('d/m/Y H:i') : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Cantidad entregada</dt>
                            <dd class="font-semibold text-gray-900">{{ $prescripcion->cantidad_entregada ?? '—' }} {{ ($prescripcion->cantidad_entregada ?? 0) === 1 ? 'unidad' : 'unidades' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Entregado por</dt>
                            <dd class="font-semibold text-gray-900">
                                {{ $prescripcion->farmaceutico ? $prescripcion->farmaceutico->nombres . ' ' . $prescripcion->farmaceutico->apellidos : '—' }}
                            </dd>
                        </div>
                        @if ($prescripcion->observacion_entrega)
                            <div class="sm:col-span-2">
                                <dt class="text-gray-500">Observación</dt>
                                <dd class="font-semibold text-gray-900">{{ $prescripcion->observacion_entrega }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>

        {{-- Columna lateral: registro de entrega --}}
        <div class="lg:col-span-1">
            @if (!$esEntregada)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 lg:sticky lg:top-6">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Registrar entrega</h3>
                    <p class="text-sm text-gray-500 mb-5">Confirma la entrega del medicamento al paciente</p>

                    @if ($prescripcion->fecha_limite)
                        <div class="bg-amber-50 text-amber-700 text-xs rounded-xl px-4 py-3 mb-5 border border-amber-100">
                            Fecha límite para reclamar: <strong>{{ $prescripcion->fecha_limite->format('d/m/Y') }}</strong>
                        </div>
                    @endif

                    @if ($stock !== null)
                        <div class="flex items-center justify-between rounded-xl px-4 py-3 mb-5 border
                                    {{ $stock > 0 ? 'bg-teal-50 border-teal-100' : 'bg-rose-50 border-rose-100' }}">
                            <span class="text-sm font-semibold {{ $stock > 0 ? 'text-teal-700' : 'text-rose-700' }}">Stock disponible</span>
                            <span class="text-lg font-extrabold tabular-nums {{ $stock > 0 ? 'text-teal-700' : 'text-rose-700' }}">{{ $stock }}</span>
                        </div>

                        @if ($stock === 0)
                            <div class="bg-rose-50 text-rose-700 text-xs rounded-xl px-4 py-3 mb-5 border border-rose-100">
                                No hay unidades registradas de este medicamento.
                                <a href="{{ route('farmacia.inventario.index', ['buscar' => $prescripcion->id_medicamento]) }}" class="font-semibold underline">Registra el inventario</a>
                                antes de entregar.
                            </div>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('farmacia.prescripciones.entregar', $prescripcion->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="cantidad_entregada" class="block text-sm font-semibold text-gray-700 mb-1.5">Cantidad entregada</label>
                            <input type="number" id="cantidad_entregada" name="cantidad_entregada" min="1" max="1000"
                                   value="{{ old('cantidad_entregada', 1) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>

                        <div>
                            <label for="observacion_entrega" class="block text-sm font-semibold text-gray-700 mb-1.5">Observación <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <textarea id="observacion_entrega" name="observacion_entrega" rows="3" maxlength="255"
                                      placeholder="Ej: se entregó al paciente / a un familiar..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observacion_entrega') }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-5 h-5'])
                            Confirmar entrega
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-teal-50 border border-teal-100 rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center mx-auto mb-3">
                        @include('admin.partials.icono', ['icono' => 'entrega', 'clase' => 'w-7 h-7 text-teal-600'])
                    </div>
                    <p class="text-sm font-semibold text-teal-800">Esta prescripción ya fue entregada</p>
                    <p class="text-xs text-teal-700 mt-1">No se puede registrar una entrega dos veces.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
