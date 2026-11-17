@extends('medico.layout')

@section('titulo', 'Atender cita')
@section('encabezado', 'Atención de la cita')

@section('contenido')

    @if ($citaAtendida)
        <div class="bg-blue-50 text-blue-800 text-sm rounded-lg px-4 py-3 mb-4 border border-blue-100">
            Esta cita ya fue marcada como atendida. Puedes consultar la información, pero ya no se permite modificarla.
        </div>
    @endif

    <a href="{{ route('medico.inicio') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-700 hover:text-teal-900 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Volver a mis citas
    </a>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">{{ session('exito') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    {{-- Ficha de la cita y del paciente --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold flex-shrink-0">
                    {{ $cita->paciente ? strtoupper(substr($cita->paciente->nombres, 0, 1) . substr($cita->paciente->apellidos, 0, 1)) : '?' }}
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-bold text-gray-900 truncate">
                        {{ $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Paciente no encontrado' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        C.C. {{ $cita->cedula_usuario }}
                        @if ($cita->paciente && $cita->paciente->edad) · {{ $cita->paciente->edad }} años @endif
                        @if ($cita->paciente) · {{ $cita->paciente->telefono ?: 'Sin teléfono' }} @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $cita->fecha->format('d/m/Y') }} · {{ substr($cita->hora_inicio, 0, 5) }}–{{ substr($cita->hora_fin, 0, 5) }}
                        · {{ $cita->consultorio->nombre ?? 'Sin consultorio' }}
                        · {{ $cita->tipoCita->nombre ?? 'Consulta' }}
                    </p>
                    @if ($cita->motivo)
                        <p class="text-xs text-gray-500 mt-1"><strong>Motivo:</strong> {{ $cita->motivo }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    {{ $cita->estadoCita->nombre ?? 'Pendiente' }}
                </span>
                @if ($citaAtendida)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Solo lectura
                    </span>
                @else
                    <form method="POST" action="{{ route('medico.cita.atendida', $cita->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Marcar como atendida
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Pestañas --}}
    @php $tabInicial = old('_tab', 'historia'); @endphp

    <div class="flex flex-wrap gap-2 mb-4" id="tabs-cita">
        <button type="button" data-tab="historia"
                class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
            @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
            Historia clínica
            <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $historias->count() }}</span>
        </button>
        <button type="button" data-tab="procedimiento"
                class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
            @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
            Procedimientos
            <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $procedimientos->count() }}</span>
        </button>
        <button type="button" data-tab="medicamento"
                class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
            @include('admin.partials.icono', ['icono' => 'medicamentos', 'clase' => 'w-4 h-4'])
            Medicamentos
            <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $prescripciones->count() }}</span>
        </button>
    </div>

    {{-- ===================== HISTORIA CLÍNICA ===================== --}}
    <section id="panel-historia" class="tab-panel">
        <div class="grid lg:grid-cols-2 gap-6">
            <div class="space-y-6">
                @unless ($citaAtendida)
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                        <h3 class="font-bold text-gray-900 mb-4">Registrar historia clínica</h3>
                        <form method="POST" action="{{ route('medico.cita.historia', $cita->id) }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="_tab" value="historia">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha *</label>
                                <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Motivo de consulta</label>
                                <textarea name="motivo_consulta" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('motivo_consulta', $cita->motivo) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Antecedentes</label>
                                <textarea name="antecedentes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('antecedentes') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Examen físico</label>
                                <textarea name="examen_fisico" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('examen_fisico') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Observaciones</label>
                                <textarea name="observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observaciones') }}</textarea>
                            </div>
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                                Guardar historia clínica
                            </button>
                        </form>
                    </div>
                @endunless
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Historia de esta cita</h3>
                @forelse ($historias as $historia)
                    <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-sm font-semibold text-gray-900">{{ $historia->fecha->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-xs text-gray-600 space-y-1">
                            @if ($historia->motivo_consulta)<p><strong>Motivo:</strong> {{ $historia->motivo_consulta }}</p>@endif
                            @if ($historia->antecedentes)<p><strong>Antecedentes:</strong> {{ $historia->antecedentes }}</p>@endif
                            @if ($historia->examen_fisico)<p><strong>Examen físico:</strong> {{ $historia->examen_fisico }}</p>@endif
                            @if ($historia->observaciones)<p><strong>Observaciones:</strong> {{ $historia->observaciones }}</p>@endif
                        </div>

                        @if ($historia->diagnosticos->count())
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Diagnósticos</p>
                                @foreach ($historia->diagnosticos as $diagnostico)
                                    <span class="inline-block text-xs text-teal-700 bg-teal-50 border border-teal-100 rounded-full px-2 py-0.5 mr-1 mb-1">{{ $diagnostico->descripcion }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($historia->enfermedades->count())
                            <div class="mt-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Enfermedades</p>
                                @foreach ($historia->enfermedades as $enfermedad)
                                    <span class="inline-block text-xs text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-full px-2 py-0.5 mr-1 mb-1">{{ $enfermedad->nombre }}</span>
                                @endforeach
                            </div>
                        @endif

                        @unless ($citaAtendida)
                            @if ((int) ($_SESSION['medico_cedula'] ?? 0) === (int) $historia->cedula_medico)
                                <details class="mt-3" @if (old('_edit') == $historia->id && $errors->any()) open @endif>
                                    <summary class="cursor-pointer select-none text-xs font-semibold text-teal-700 hover:text-teal-900">Editar historia clínica</summary>
                                    <form method="POST" action="{{ route('medico.historia.update', $historia->id) }}" class="space-y-3 mt-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_edit" value="{{ $historia->id }}">
                                        <input type="hidden" name="_tab" value="historia">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha *</label>
                                            <input type="date" name="fecha" value="{{ old('fecha', $historia->fecha->format('Y-m-d')) }}" required
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Motivo de consulta</label>
                                            <textarea name="motivo_consulta" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Antecedentes</label>
                                            <textarea name="antecedentes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('antecedentes', $historia->antecedentes) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Examen físico</label>
                                            <textarea name="examen_fisico" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('examen_fisico', $historia->examen_fisico) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Observaciones</label>
                                            <textarea name="observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observaciones', $historia->observaciones) }}</textarea>
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-teal-700 transition">
                                            Actualizar historia
                                        </button>
                                    </form>
                                </details>
                            @endif
                        @endunless
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Aún no has registrado la historia clínica de esta cita.</p>
                @endforelse

                @if ($historias->count())
                    @unless ($citaAtendida)
                        <div class="mt-6 pt-5 border-t border-gray-100 grid sm:grid-cols-2 gap-5">
                            {{-- Diagnóstico --}}
                            <form method="POST" action="{{ route('medico.cita.diagnostico', $cita->id) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="_tab" value="historia">
                                <p class="text-sm font-semibold text-gray-800">Agregar diagnóstico</p>
                                <input type="text" name="descripcion" required maxlength="255" placeholder="Ej: Faringitis aguda"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <input type="hidden" name="fecha" value="{{ now()->toDateString() }}">
                                <select name="id_historia_clinica"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    @foreach ($historias as $historia)
                                        <option value="{{ $historia->id }}">Historia {{ $historia->fecha->format('d/m/Y') }}</option>
                                    @endforeach
                                </select>
                                <button class="w-full bg-teal-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-teal-700 transition">Guardar diagnóstico</button>
                            </form>

                            {{-- Enfermedad --}}
                            <form method="POST" action="{{ route('medico.cita.enfermedad', $cita->id) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="_tab" value="historia">
                                <p class="text-sm font-semibold text-gray-800">Agregar enfermedad</p>
                                <input type="text" name="nombre" required maxlength="150" placeholder="Ej: Hipertensión arterial"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <input type="hidden" name="fecha" value="{{ now()->toDateString() }}">
                                <select name="id_historia_clinica"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    @foreach ($historias as $historia)
                                        <option value="{{ $historia->id }}">Historia {{ $historia->fecha->format('d/m/Y') }}</option>
                                    @endforeach
                                </select>
                                <button class="w-full bg-indigo-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-indigo-700 transition">Guardar enfermedad</button>
                            </form>
                        </div>
                    @endunless
                @endif
            </div>
        </div>
    </section>

    {{-- ===================== PROCEDIMIENTO ===================== --}}
    <section id="panel-procedimiento" class="tab-panel hidden">
        <div class="grid lg:grid-cols-2 gap-6">
            @unless ($citaAtendida)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Registrar procedimiento</h3>
                    <form method="POST" action="{{ route('medico.cita.procedimiento', $cita->id) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="procedimiento">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de procedimiento</label>
                            <select name="id_tipo_procedimiento"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Selecciona...</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('id_tipo_procedimiento') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción *</label>
                            <input type="text" name="descripcion" value="{{ old('descripcion') }}" required maxlength="255"
                                   placeholder="Ej: Sutura de herida"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observaciones') }}</textarea>
                        </div>
                        <input type="hidden" name="fecha" value="{{ now()->toDateString() }}">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Guardar procedimiento
                        </button>
                    </form>
                </div>
            @endunless

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Procedimientos de esta cita</h3>
                @forelse ($procedimientos as $procedimiento)
                    <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $procedimiento->descripcion }}</span>
                            <span class="text-xs text-gray-400">{{ $procedimiento->fecha->format('d/m/Y') }}</span>
                        </div>
                        @if ($procedimiento->tipoProcedimiento)
                            <span class="inline-block text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-full">{{ $procedimiento->tipoProcedimiento->nombre }}</span>
                        @endif
                        @if ($procedimiento->observaciones)
                            <p class="text-xs text-gray-600 mt-2">{{ $procedimiento->observaciones }}</p>
                        @endif

                        @unless ($citaAtendida)
                            @if ((int) ($_SESSION['medico_cedula'] ?? 0) === (int) $procedimiento->cedula_medico)
                                <details class="mt-3" @if (old('_edit') == $procedimiento->id && $errors->any()) open @endif>
                                    <summary class="cursor-pointer select-none text-xs font-semibold text-teal-700 hover:text-teal-900">Editar procedimiento</summary>
                                    <form method="POST" action="{{ route('medico.procedimiento.update', $procedimiento->id) }}" class="space-y-3 mt-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_edit" value="{{ $procedimiento->id }}">
                                        <input type="hidden" name="_tab" value="procedimiento">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de procedimiento</label>
                                            <select name="id_tipo_procedimiento"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                                <option value="">Sin tipo</option>
                                                @foreach ($tipos as $tipo)
                                                    <option value="{{ $tipo->id }}" {{ old('id_tipo_procedimiento', $procedimiento->id_tipo_procedimiento) == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Descripción *</label>
                                            <input type="text" name="descripcion" value="{{ old('descripcion', $procedimiento->descripcion) }}" required maxlength="255"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Observaciones</label>
                                            <textarea name="observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observaciones', $procedimiento->observaciones) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha *</label>
                                            <input type="date" name="fecha" value="{{ old('fecha', $procedimiento->fecha->format('Y-m-d')) }}" required
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-teal-700 transition">
                                            Actualizar procedimiento
                                        </button>
                                    </form>
                                </details>
                            @endif
                        @endunless
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Aún no has registrado procedimientos en esta cita.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===================== MEDICAMENTOS ===================== --}}
    <section id="panel-medicamento" class="tab-panel hidden">
        <div class="grid lg:grid-cols-2 gap-6">
            @unless ($citaAtendida)
                @php
                    $filasMedicamentos = old('medicamentos', [
                        ['id_medicamento' => '', 'dosis' => '', 'frecuencia' => '', 'duracion' => '', 'fecha_limite' => '', 'indicaciones' => ''],
                    ]);
                @endphp
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Formular medicamentos</h3>
                    <form method="POST" action="{{ route('medico.cita.prescripcion', $cita->id) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="medicamento">
                        <div id="medicamentos-container" class="space-y-4">
                            @foreach ($filasMedicamentos as $indice => $fila)
                                <div class="medicamento-fila border border-gray-200 rounded-xl p-4 space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-gray-500">Medicamento {{ $loop->iteration }}</span>
                                        <button type="button" class="quitar-medicamento text-xs font-semibold text-rose-600 hover:text-rose-800">Quitar</button>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Medicamento *</label>
                                        <select name="medicamentos[{{ $indice }}][id_medicamento]" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                            <option value="">Selecciona un medicamento del catálogo</option>
                                            @foreach ($medicamentos as $medicamento)
                                                <option value="{{ $medicamento->codigo }}" {{ ($fila['id_medicamento'] ?? '') == $medicamento->codigo ? 'selected' : '' }}>
                                                    {{ $medicamento->nombre }}{{ $medicamento->presentacion ? ' — ' . $medicamento->presentacion : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("medicamentos.$indice.id_medicamento")
                                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                        @enderror
                                        @if ($medicamentos->isEmpty())
                                            <p class="text-xs text-amber-600 mt-1">No hay medicamentos activos en el catálogo.</p>
                                        @endif
                                    </div>
                                    <div class="grid sm:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Dosis</label>
                                            <input type="text" name="medicamentos[{{ $indice }}][dosis]" value="{{ $fila['dosis'] ?? '' }}" maxlength="100" placeholder="500 mg"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Frecuencia</label>
                                            <input type="text" name="medicamentos[{{ $indice }}][frecuencia]" value="{{ $fila['frecuencia'] ?? '' }}" maxlength="100" placeholder="Cada 8 horas"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Duración</label>
                                            <input type="text" name="medicamentos[{{ $indice }}][duracion]" value="{{ $fila['duracion'] ?? '' }}" maxlength="100" placeholder="5 días"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha límite para reclamar *</label>
                                        <input type="date" name="medicamentos[{{ $indice }}][fecha_limite]" value="{{ $fila['fecha_limite'] ?? '' }}"
                                               min="{{ now()->toDateString() }}" required
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        @error("medicamentos.$indice.fecha_limite")
                                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Indicaciones</label>
                                        <textarea name="medicamentos[{{ $indice }}][indicaciones]" rows="2" placeholder="Ej: Tomar después de las comidas"
                                                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ $fila['indicaciones'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400">Pasada la fecha límite, el medicamento deja de aparecer en farmacia.</p>
                        <button type="button" id="agregar-medicamento"
                                class="w-full inline-flex items-center justify-center gap-2 border border-teal-600 text-teal-700 text-sm font-semibold py-2.5 rounded-lg hover:bg-teal-50 transition">
                            + Agregar otro medicamento
                        </button>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Formular medicamento(s)
                        </button>
                    </form>
                </div>

                <template id="plantilla-medicamento">
                    <div class="medicamento-fila border border-gray-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-gray-500">Medicamento nuevo</span>
                            <button type="button" class="quitar-medicamento text-xs font-semibold text-rose-600 hover:text-rose-800">Quitar</button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Medicamento *</label>
                            <select name="medicamentos[__INDICE__][id_medicamento]" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Selecciona un medicamento del catálogo</option>
                                @foreach ($medicamentos as $medicamento)
                                    <option value="{{ $medicamento->codigo }}">
                                        {{ $medicamento->nombre }}{{ $medicamento->presentacion ? ' — ' . $medicamento->presentacion : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($medicamentos->isEmpty())
                                <p class="text-xs text-amber-600 mt-1">No hay medicamentos activos en el catálogo.</p>
                            @endif
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dosis</label>
                                <input type="text" name="medicamentos[__INDICE__][dosis]" maxlength="100" placeholder="500 mg"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Frecuencia</label>
                                <input type="text" name="medicamentos[__INDICE__][frecuencia]" maxlength="100" placeholder="Cada 8 horas"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Duración</label>
                                <input type="text" name="medicamentos[__INDICE__][duracion]" maxlength="100" placeholder="5 días"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha límite para reclamar *</label>
                            <input type="date" name="medicamentos[__INDICE__][fecha_limite]" min="{{ now()->toDateString() }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Indicaciones</label>
                            <textarea name="medicamentos[__INDICE__][indicaciones]" rows="2" placeholder="Ej: Tomar después de las comidas"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"></textarea>
                        </div>
                    </div>
                </template>
            @endunless

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Fórmula de esta cita</h3>
                @forelse ($prescripciones as $prescripcion)
                    <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $prescripcion->medicamento }}</span>
                            <span class="text-xs text-gray-400">{{ $prescripcion->fecha->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-xs text-gray-600 space-y-0.5">
                            @if ($prescripcion->dosis)<p><strong>Dosis:</strong> {{ $prescripcion->dosis }}</p>@endif
                            @if ($prescripcion->frecuencia)<p><strong>Frecuencia:</strong> {{ $prescripcion->frecuencia }}</p>@endif
                            @if ($prescripcion->duracion)<p><strong>Duración:</strong> {{ $prescripcion->duracion }}</p>@endif
                            <p><strong>Fecha límite:</strong> {{ $prescripcion->fecha_limite ? $prescripcion->fecha_limite->format('d/m/Y') : '—' }}</p>
                            @if ($prescripcion->indicaciones)<p><strong>Indicaciones:</strong> {{ $prescripcion->indicaciones }}</p>@endif
                        </div>

                        @unless ($citaAtendida)
                            @if ((int) ($_SESSION['medico_cedula'] ?? 0) === (int) $prescripcion->cedula_medico)
                                <details class="mt-3" @if (old('_edit') == $prescripcion->id && $errors->any()) open @endif>
                                    <summary class="cursor-pointer select-none text-xs font-semibold text-teal-700 hover:text-teal-900">Editar medicamento</summary>
                                    <form method="POST" action="{{ route('medico.prescripcion.update', $prescripcion->id) }}" class="space-y-3 mt-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_edit" value="{{ $prescripcion->id }}">
                                        <input type="hidden" name="_tab" value="medicamento">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Medicamento *</label>
                                            <select name="id_medicamento" required
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                                <option value="">Selecciona un medicamento</option>
                                                @foreach ($medicamentos as $medicamento)
                                                    <option value="{{ $medicamento->codigo }}" {{ old('id_medicamento', $prescripcion->id_medicamento) == $medicamento->codigo ? 'selected' : '' }}>
                                                        {{ $medicamento->nombre }}{{ $medicamento->presentacion ? ' — ' . $medicamento->presentacion : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Dosis</label>
                                                <input type="text" name="dosis" value="{{ old('dosis', $prescripcion->dosis) }}" maxlength="100"
                                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Frecuencia</label>
                                                <input type="text" name="frecuencia" value="{{ old('frecuencia', $prescripcion->frecuencia) }}" maxlength="100"
                                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Duración</label>
                                                <input type="text" name="duracion" value="{{ old('duracion', $prescripcion->duracion) }}" maxlength="100"
                                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha límite para reclamar *</label>
                                            <input type="date" name="fecha_limite"
                                                   value="{{ old('fecha_limite', $prescripcion->fecha_limite ? $prescripcion->fecha_limite->format('Y-m-d') : '') }}"
                                                   min="{{ now()->toDateString() }}" required
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                            @error('fecha_limite')
                                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Indicaciones</label>
                                            <textarea name="indicaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('indicaciones', $prescripcion->indicaciones) }}</textarea>
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-teal-700 transition">
                                            Actualizar medicamento
                                        </button>
                                    </form>
                                </details>
                            @endif
                        @endunless
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Aún no has formulado medicamentos en esta cita.</p>
                @endforelse
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        const botones = document.querySelectorAll('#tabs-cita .tab-boton');
        const paneles = document.querySelectorAll('.tab-panel');

        function activarTab(nombre) {
            botones.forEach(function (b) {
                const activo = b.getAttribute('data-tab') === nombre;
                b.classList.toggle('bg-teal-600', activo);
                b.classList.toggle('text-white', activo);
                b.classList.toggle('border-teal-600', activo);
                b.classList.toggle('bg-white', !activo);
                b.classList.toggle('text-gray-600', !activo);
                b.classList.toggle('border-gray-200', !activo);
            });
            paneles.forEach(function (p) {
                p.classList.toggle('hidden', p.id !== 'panel-' + nombre);
            });
        }

        botones.forEach(function (b) {
            b.addEventListener('click', function () { activarTab(b.getAttribute('data-tab')); });
        });

        activarTab('{{ $tabInicial }}');

        @unless ($citaAtendida)
            const contenedorMedicamentos = document.getElementById('medicamentos-container');
            const plantillaMedicamento = document.getElementById('plantilla-medicamento');
            let indiceMedicamento = {{ count($filasMedicamentos) }};

            document.getElementById('agregar-medicamento').addEventListener('click', function () {
                const html = plantillaMedicamento.innerHTML.replace(/__INDICE__/g, indiceMedicamento);
                contenedorMedicamentos.insertAdjacentHTML('beforeend', html);
                indiceMedicamento++;
            });

            contenedorMedicamentos.addEventListener('click', function (evento) {
                const boton = evento.target.closest('.quitar-medicamento');
                if (!boton) { return; }

                if (contenedorMedicamentos.querySelectorAll('.medicamento-fila').length <= 1) {
                    alert('Debe dejar al menos un medicamento.');
                    return;
                }

                boton.closest('.medicamento-fila').remove();
            });
        @endunless
    </script>
@endpush
