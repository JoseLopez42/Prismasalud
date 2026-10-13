@extends('medico.layout')

@section('titulo', 'Procedimientos')
@section('encabezado', 'Procedimientos')

@section('contenido')

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900">Procedimientos del paciente</h2>
        <p class="text-sm text-gray-500">Busca por cédula y registra el procedimiento, su tipo y su detalle</p>
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
    <form method="GET" action="{{ route('medico.procedimiento.index') }}"
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
                @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
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
        {{-- Ficha del paciente --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold flex-shrink-0">
                    {{ strtoupper(substr($paciente->nombres, 0, 1) . substr($paciente->apellidos, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-bold text-gray-900 truncate">{{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
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

        @php $tabInicial = old('_tab', 'procedimiento'); @endphp

        <div class="flex flex-wrap gap-2 mb-4" id="tabs-procedimiento">
            <button type="button" data-tab="procedimiento"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
                @include('admin.partials.icono', ['icono' => 'procedimientos', 'clase' => 'w-4 h-4'])
                Procedimiento
                <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $procedimientos->count() }}</span>
            </button>
            <button type="button" data-tab="tipo"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
                @include('admin.partials.icono', ['icono' => 'lista', 'clase' => 'w-4 h-4'])
                Tipo de procedimiento
                <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $tipos->count() }}</span>
            </button>
            <button type="button" data-tab="detalle"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition">
                @include('admin.partials.icono', ['icono' => 'lista', 'clase' => 'w-4 h-4'])
                Detalle de procedimiento
                <span class="text-xs bg-white/70 text-gray-500 rounded-full px-2 py-0.5">{{ $detalles->count() }}</span>
            </button>
        </div>

        {{-- ===================== PROCEDIMIENTO ===================== --}}
        <section id="panel-procedimiento" class="tab-panel">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Registrar procedimiento</h3>
                    <form method="POST" action="{{ route('medico.procedimiento.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="procedimiento">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de procedimiento</label>
                            <select name="id_tipo_procedimiento"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Sin tipo</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('id_tipo_procedimiento') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción *</label>
                            <input type="text" name="descripcion" value="{{ old('descripcion') }}" maxlength="255" required
                                   placeholder="Ej: Cuadro hemático"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Observaciones</label>
                            <textarea name="observaciones" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observaciones') }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Guardar procedimiento
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Procedimientos registrados</h3>
                    @forelse ($procedimientos as $procedimiento)
                        <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $procedimiento->descripcion }}</span>
                                <span class="text-xs text-gray-400">{{ $procedimiento->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($procedimiento->tipoProcedimiento)
                                <span class="inline-block text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-full">{{ $procedimiento->tipoProcedimiento->nombre }}</span>
                            @endif
                            @if ($procedimiento->observaciones)
                                <p class="text-xs text-gray-600 mt-1">{{ $procedimiento->observaciones }}</p>
                            @endif
                            @if ($procedimiento->detalles->isNotEmpty())
                                <p class="text-xs text-gray-400 mt-1">{{ $procedimiento->detalles->count() }} detalle(s)</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">Dr(a). {{ $procedimiento->medico->nombres ?? '' }} {{ $procedimiento->medico->apellidos ?? '' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Este paciente no tiene procedimientos registrados.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===================== TIPO DE PROCEDIMIENTO ===================== --}}
        <section id="panel-tipo" class="tab-panel hidden">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Crear tipo de procedimiento</h3>
                    <form method="POST" action="{{ route('medico.tipo_procedimiento.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="tipo">
                        <input type="hidden" name="cedula" value="{{ $paciente->cedula }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" maxlength="150" required
                                   placeholder="Ej: Laboratorio clínico"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                            <textarea name="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('descripcion') }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            Guardar tipo
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Catálogo de tipos</h3>
                    @forelse ($tipos as $tipo)
                        <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $tipo->nombre }}</p>
                            @if ($tipo->descripcion)
                                <p class="text-xs text-gray-600 mt-1">{{ $tipo->descripcion }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Todavía no hay tipos de procedimiento.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===================== DETALLE DE PROCEDIMIENTO ===================== --}}
        <section id="panel-detalle" class="tab-panel hidden">
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Registrar detalle</h3>
                    @if ($procedimientos->isEmpty())
                        <p class="text-sm text-amber-600 bg-amber-50 rounded-md px-4 py-3">
                            Primero registra un procedimiento para este paciente.
                        </p>
                    @else
                        <form method="POST" action="{{ route('medico.detalle_procedimiento.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="_tab" value="detalle">
                            <input type="hidden" name="cedula" value="{{ $paciente->cedula }}">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Procedimiento *</label>
                                <select name="id_procedimiento" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Selecciona un procedimiento...</option>
                                    @foreach ($procedimientos as $procedimiento)
                                        <option value="{{ $procedimiento->id }}" {{ old('id_procedimiento') == $procedimiento->id ? 'selected' : '' }}>
                                            {{ $procedimiento->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($procedimiento->descripcion, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción *</label>
                                <input type="text" name="descripcion" value="{{ old('descripcion') }}" maxlength="255" required
                                       placeholder="Ej: Hemoglobina"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cantidad *</label>
                                <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" min="1" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Resultado</label>
                                <textarea name="resultado" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('resultado') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                                Guardar detalle
                            </button>
                        </form>
                    @endif
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Detalles registrados</h3>
                    @forelse ($detalles as $detalle)
                        <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $detalle->descripcion }}</span>
                                <span class="text-xs text-gray-400">x{{ $detalle->cantidad }}</span>
                            </div>
                            <p class="text-xs text-gray-500">Procedimiento: {{ \Illuminate\Support\Str::limit($detalle->procedimiento->descripcion ?? '—', 40) }}</p>
                            @if ($detalle->resultado)
                                <p class="text-xs text-gray-600 mt-1"><strong>Resultado:</strong> {{ $detalle->resultado }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Este paciente no tiene detalles registrados.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
    @if ($paciente)
    <script>
        const botones = document.querySelectorAll('#tabs-procedimiento .tab-boton');
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
