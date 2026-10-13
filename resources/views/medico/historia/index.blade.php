@extends('medico.layout')

@section('titulo', 'Historia médica')
@section('encabezado', 'Historia médica')

@section('contenido')

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900">Historia médica del paciente</h2>
        <p class="text-sm text-gray-500">Busca por cédula y registra la historia clínica, los diagnósticos y las enfermedades</p>
    </div>

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Buscador por cédula --}}
    <form method="GET" action="{{ route('medico.historia.index') }}"
          class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Buscar paciente por cédula</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="number" name="cedula" value="{{ $cedulaBuscada }}"
                       placeholder="Ej: 1065581109" min="1" required
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
            </div>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                Buscar
            </button>
        </div>
    </form>

    @if ($cedulaBuscada !== '' && !$paciente)
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-xl px-5 py-6 text-center">
            No se encontró ningún paciente con la cédula <strong>{{ $cedulaBuscada }}</strong>.
        </div>
    @endif

    @if ($paciente)
        @php
            $nombrePaciente = $paciente->nombres . ' ' . $paciente->apellidos;
        @endphp

        {{-- Ficha del paciente --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold flex-shrink-0">
                    {{ strtoupper(substr($paciente->nombres, 0, 1) . substr($paciente->apellidos, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-bold text-gray-900 truncate">{{ $nombrePaciente }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $paciente->tipoDocumento->nombre ?? 'C.C.' }} {{ $paciente->cedula }}
                        @if ($paciente->edad) · {{ $paciente->edad }} años @endif
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $paciente->correo ?? 'Sin correo' }} @if ($paciente->telefono) · {{ $paciente->telefono }} @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Pestañas --}}
        @php $tabInicial = old('_tab', 'historia'); @endphp

        <div class="flex flex-wrap gap-2 mb-4" id="tabs-historia">
            <button type="button" data-tab="historia"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
                @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                Historia clínica
                <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $historias->count() }}</span>
            </button>
            <button type="button" data-tab="diagnostico"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
                @include('admin.partials.icono', ['icono' => 'lista', 'clase' => 'w-4 h-4'])
                Diagnóstico
                <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $diagnosticos->count() }}</span>
            </button>
            <button type="button" data-tab="enfermedad"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
                @include('admin.partials.icono', ['icono' => 'eps', 'clase' => 'w-4 h-4'])
                Enfermedad
                <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $enfermedades->count() }}</span>
            </button>
        </div>

        {{-- ===================== HISTORIA CLÍNICA ===================== --}}
        <section id="panel-historia" class="tab-panel">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Registrar historia clínica</h3>
                    <form method="POST" action="{{ route('medico.historia.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="historia">
                        <input type="hidden" name="cedula_paciente" value="{{ $paciente->cedula }}">
                        @if ($idCita)
                            <input type="hidden" name="id_cita" value="{{ $idCita }}">
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha *</label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Motivo de consulta</label>
                            <textarea name="motivo_consulta" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('motivo_consulta') }}</textarea>
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

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Historial registrado</h3>
                    @forelse ($historias as $historia)
                        <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $historia->fecha->format('d/m/Y') }}</span>
                                <span class="text-xs text-gray-400">Dr(a). {{ $historia->medico->nombres ?? '' }} {{ $historia->medico->apellidos ?? '' }}</span>
                            </div>
                            <div class="text-xs text-gray-600 space-y-1">
                                @if ($historia->motivo_consulta)<p><strong>Motivo:</strong> {{ $historia->motivo_consulta }}</p>@endif
                                @if ($historia->antecedentes)<p><strong>Antecedentes:</strong> {{ $historia->antecedentes }}</p>@endif
                                @if ($historia->examen_fisico)<p><strong>Examen físico:</strong> {{ $historia->examen_fisico }}</p>@endif
                                @if ($historia->observaciones)<p><strong>Observaciones:</strong> {{ $historia->observaciones }}</p>@endif
                            </div>

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
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Este paciente no tiene historias clínicas registradas.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===================== DIAGNÓSTICO ===================== --}}
        <section id="panel-diagnostico" class="tab-panel hidden">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Registrar diagnóstico</h3>
                    <form method="POST" action="{{ route('medico.diagnostico.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="diagnostico">
                        <input type="hidden" name="cedula_paciente" value="{{ $paciente->cedula }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha *</label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción *</label>
                            <input type="text" name="descripcion" value="{{ old('descripcion') }}" maxlength="255" required
                                   placeholder="Ej: Faringitis aguda"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo</label>
                            <select name="tipo"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Selecciona...</option>
                                @foreach (['Presuntivo', 'Confirmado', 'Definitivo'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Historia clínica <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <select name="id_historia_clinica"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Sin asociar</option>
                                @foreach ($historias as $historia)
                                    <option value="{{ $historia->id }}" {{ old('id_historia_clinica') == $historia->id ? 'selected' : '' }}>
                                        {{ $historia->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($historia->motivo_consulta ?: 'Sin motivo', 40) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Guardar diagnóstico
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Diagnósticos registrados</h3>
                    @forelse ($diagnosticos as $diagnostico)
                        <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $diagnostico->descripcion }}</span>
                                <span class="text-xs text-gray-400">{{ $diagnostico->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($diagnostico->tipo)
                                <span class="inline-block text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-full">{{ $diagnostico->tipo }}</span>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">Dr(a). {{ $diagnostico->medico->nombres ?? '' }} {{ $diagnostico->medico->apellidos ?? '' }}</p>

                            @if ((int) ($_SESSION['medico_cedula'] ?? 0) === (int) $diagnostico->cedula_medico)
                                <details class="mt-3" @if (old('_edit') == $diagnostico->id && $errors->any()) open @endif>
                                    <summary class="cursor-pointer select-none text-xs font-semibold text-teal-700 hover:text-teal-900">Editar diagnóstico</summary>
                                    <form method="POST" action="{{ route('medico.diagnostico.update', $diagnostico->id) }}" class="space-y-3 mt-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_edit" value="{{ $diagnostico->id }}">
                                        <input type="hidden" name="_tab" value="diagnostico">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha *</label>
                                            <input type="date" name="fecha" value="{{ old('fecha', $diagnostico->fecha->format('Y-m-d')) }}" required
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Descripción *</label>
                                            <input type="text" name="descripcion" value="{{ old('descripcion', $diagnostico->descripcion) }}" required maxlength="255"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                                            <select name="tipo"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                                <option value="">Selecciona...</option>
                                                @foreach (['Presuntivo', 'Confirmado', 'Definitivo'] as $tipoOpcion)
                                                    <option value="{{ $tipoOpcion }}" {{ old('tipo', $diagnostico->tipo) === $tipoOpcion ? 'selected' : '' }}>{{ $tipoOpcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Historia clínica <span class="text-gray-400 font-normal">(opcional)</span></label>
                                            <select name="id_historia_clinica"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                                <option value="">Sin asociar</option>
                                                @foreach ($historias as $historia)
                                                    <option value="{{ $historia->id }}" {{ old('id_historia_clinica', $diagnostico->id_historia_clinica) == $historia->id ? 'selected' : '' }}>
                                                        {{ $historia->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($historia->motivo_consulta ?: 'Sin motivo', 40) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-teal-700 transition">
                                            Actualizar diagnóstico
                                        </button>
                                    </form>
                                </details>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Este paciente no tiene diagnósticos registrados.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===================== ENFERMEDAD ===================== --}}
        <section id="panel-enfermedad" class="tab-panel hidden">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Registrar enfermedad</h3>
                    <form method="POST" action="{{ route('medico.enfermedad.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="enfermedad">
                        <input type="hidden" name="cedula_paciente" value="{{ $paciente->cedula }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha *</label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de la enfermedad *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" maxlength="150" required
                                   placeholder="Ej: Hipertensión arterial"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                            <textarea name="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('descripcion') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Historia clínica <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <select name="id_historia_clinica"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Sin asociar</option>
                                @foreach ($historias as $historia)
                                    <option value="{{ $historia->id }}" {{ old('id_historia_clinica') == $historia->id ? 'selected' : '' }}>
                                        {{ $historia->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($historia->motivo_consulta ?: 'Sin motivo', 40) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Guardar enfermedad
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Enfermedades registradas</h3>
                    @forelse ($enfermedades as $enfermedad)
                        <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $enfermedad->nombre }}</span>
                                <span class="text-xs text-gray-400">{{ $enfermedad->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($enfermedad->descripcion)
                                <p class="text-xs text-gray-600">{{ $enfermedad->descripcion }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">Dr(a). {{ $enfermedad->medico->nombres ?? '' }} {{ $enfermedad->medico->apellidos ?? '' }}</p>

                            @if ((int) ($_SESSION['medico_cedula'] ?? 0) === (int) $enfermedad->cedula_medico)
                                <details class="mt-3" @if (old('_edit') == $enfermedad->id && $errors->any()) open @endif>
                                    <summary class="cursor-pointer select-none text-xs font-semibold text-teal-700 hover:text-teal-900">Editar enfermedad</summary>
                                    <form method="POST" action="{{ route('medico.enfermedad.update', $enfermedad->id) }}" class="space-y-3 mt-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_edit" value="{{ $enfermedad->id }}">
                                        <input type="hidden" name="_tab" value="enfermedad">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha *</label>
                                            <input type="date" name="fecha" value="{{ old('fecha', $enfermedad->fecha->format('Y-m-d')) }}" required
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Nombre de la enfermedad *</label>
                                            <input type="text" name="nombre" value="{{ old('nombre', $enfermedad->nombre) }}" required maxlength="150"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                                            <textarea name="descripcion" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('descripcion', $enfermedad->descripcion) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Historia clínica <span class="text-gray-400 font-normal">(opcional)</span></label>
                                            <select name="id_historia_clinica"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                                <option value="">Sin asociar</option>
                                                @foreach ($historias as $historia)
                                                    <option value="{{ $historia->id }}" {{ old('id_historia_clinica', $enfermedad->id_historia_clinica) == $historia->id ? 'selected' : '' }}>
                                                        {{ $historia->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($historia->motivo_consulta ?: 'Sin motivo', 40) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-teal-700 transition">
                                            Actualizar enfermedad
                                        </button>
                                    </form>
                                </details>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Este paciente no tiene enfermedades registradas.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
    @if ($paciente)
    <script>
        const botones = document.querySelectorAll('#tabs-historia .tab-boton');
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
    </script>
    @endif
@endpush
