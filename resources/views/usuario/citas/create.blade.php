@extends('usuario.layout')

@section('titulo', 'Agendar cita')
@section('encabezado', 'Agendar cita')

@section('contenido')

    @php
        $pasoInicial = 0;
        if ($errors->any()) {
            $pasoInicial = old('hora_inicio') ? 3 : (old('cedula_medico') ? 2 : 1);
        }
    @endphp

    <div class="mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Agendar una cita médica</h2>
        <p class="text-sm text-gray-500">Elige el servicio, el profesional y el horario que prefieras. Te guiamos paso a paso.</p>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl px-4 py-3 mb-5 max-w-4xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- Barra de progreso --}}
    <div class="max-w-4xl mb-6">
        <ol id="progreso" class="flex items-center gap-2 sm:gap-4">
            @foreach (['Servicio', 'Profesional', 'Fecha y hora', 'Confirmar'] as $i => $etiqueta)
                <li class="paso-indicador flex-1 flex items-center gap-2 sm:gap-3" data-paso="{{ $i }}">
                    <span class="circulo flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold border-2 transition">{{ $i + 1 }}</span>
                    <span class="etiqueta hidden sm:block text-xs font-semibold text-gray-400 transition">{{ $etiqueta }}</span>
                    @if ($i < 3)
                        <span class="linea hidden sm:block flex-1 h-px bg-gray-200"></span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    <form method="POST" action="{{ route('usuario.citas.store') }}" id="form-cita" class="max-w-4xl" novalidate>
        @csrf
        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}">

        {{-- Paso 1: Servicio --}}
        <section class="panel-paso bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-7" data-panel="0">
            <h3 class="text-lg font-bold text-gray-900 mb-1">¿Qué servicio necesitas?</h3>
            <p class="text-sm text-gray-500 mb-5">Selecciona el tipo de atención médica.</p>

            <div class="grid sm:grid-cols-2 gap-3">
                @forelse ($tiposCita as $tipo)
                    <label class="cursor-pointer">
                        <input type="radio" name="id_tipo_cita" value="{{ $tipo->id }}"
                               data-nombre="{{ $tipo->nombre }}"
                               data-duracion="{{ $tipo->duracion_minutos }}"
                               class="peer sr-only"
                               {{ (string) old('id_tipo_cita') === (string) $tipo->id ? 'checked' : '' }}>
                        <div class="h-full border-2 border-gray-200 rounded-xl p-4 transition
                                    hover:border-teal-300 hover:bg-teal-50/40
                                    peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:ring-2 peer-checked:ring-teal-100">
                            <div class="flex items-start justify-between gap-3">
                                <span class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-5 h-5'])
                                </span>
                                <span class="text-[11px] font-semibold text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">
                                    {{ $tipo->duracion_minutos ? $tipo->duracion_minutos . ' min' : '30 min' }}
                                </span>
                            </div>
                            <p class="mt-3 font-semibold text-gray-900">{{ $tipo->nombre }}</p>
                            @if ($tipo->descripcion)
                                <p class="text-xs text-gray-500 mt-1">{{ $tipo->descripcion }}</p>
                            @endif
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-gray-400 col-span-2 py-6 text-center">No hay tipos de cita disponibles.</p>
                @endforelse
            </div>
        </section>

        {{-- Paso 2: Profesional --}}
        <section class="panel-paso hidden bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-7" data-panel="1">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Elige tu profesional</h3>
            <p class="text-sm text-gray-500 mb-4">Filtra por especialidad y selecciona el médico que te atenderá.</p>

            <div class="flex flex-wrap gap-2 mb-5">
                <button type="button" class="chip-especialidad px-3 py-1.5 text-xs font-semibold rounded-full border transition bg-teal-600 text-white border-teal-600"
                        data-filtro="todos">Todas</button>
                @foreach ($especialidades as $esp)
                    <button type="button" class="chip-especialidad px-3 py-1.5 text-xs font-semibold rounded-full border transition bg-white text-gray-600 border-gray-300"
                            data-filtro="{{ $esp->id }}">{{ $esp->nombre }}</button>
                @endforeach
            </div>

            <div id="lista-medicos" class="grid sm:grid-cols-2 gap-3">
                @forelse ($medicos as $medico)
                    <label class="medico-item cursor-pointer" data-especialidad="{{ $medico->id_especialidad }}">
                        <input type="radio" name="cedula_medico" value="{{ $medico->cedula }}"
                               data-nombre="Dr(a). {{ $medico->nombres }} {{ $medico->apellidos }}"
                               data-especialidad="{{ $medico->especialidad->nombre ?? 'Sin especialidad' }}"
                               class="peer sr-only"
                               {{ (string) old('cedula_medico') === (string) $medico->cedula ? 'checked' : '' }}>
                        <div class="h-full flex items-center gap-3 border-2 border-gray-200 rounded-xl p-4 transition
                                    hover:border-teal-300 hover:bg-teal-50/40
                                    peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:ring-2 peer-checked:ring-teal-100">
                            <span class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($medico->nombres, 0, 1) . substr($medico->apellidos, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">Dr(a). {{ $medico->nombres }} {{ $medico->apellidos }}</p>
                                <p class="text-xs text-teal-700">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }}</p>
                            </div>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-gray-400 col-span-2 py-6 text-center">No hay médicos disponibles.</p>
                @endforelse
            </div>
            <p id="sin-medicos" class="hidden text-sm text-gray-400 py-6 text-center">No hay médicos en esta especialidad.</p>
        </section>

        {{-- Paso 3: Fecha y hora --}}
        <section class="panel-paso hidden bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-7" data-panel="2">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Fecha y hora</h3>
            <p class="text-sm text-gray-500 mb-5">Selecciona el día y el cupo disponible que más te convenga.</p>

            <div class="max-w-xs mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de la cita *</label>
                <input type="date" id="fecha" name="fecha"
                       value="{{ old('fecha', now()->toDateString()) }}"
                       min="{{ now()->toDateString() }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
            </div>

            <div id="cupos-info" class="hidden text-xs text-teal-700 bg-teal-50 border border-teal-100 rounded-lg px-3 py-2 mb-4"></div>

            <div id="cupos-container">
                <p class="text-sm text-gray-400 py-6 text-center">Elige un médico y una fecha para ver los cupos.</p>
            </div>
        </section>

        {{-- Paso 4: Confirmación --}}
        <section class="panel-paso hidden bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-7" data-panel="3">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Confirma tu cita</h3>
            <p class="text-sm text-gray-500 mb-5">Revisa los datos antes de agendar.</p>

            <dl class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden mb-5">
                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <dt class="text-gray-500">Servicio</dt>
                    <dd class="font-semibold text-gray-900 text-right" data-resumen="servicio">—</dd>
                </div>
                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <dt class="text-gray-500">Profesional</dt>
                    <dd class="font-semibold text-gray-900 text-right" data-resumen="medico">—</dd>
                </div>
                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <dt class="text-gray-500">Especialidad</dt>
                    <dd class="text-gray-700 text-right" data-resumen="especialidad">—</dd>
                </div>
                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <dt class="text-gray-500">Fecha y hora</dt>
                    <dd class="font-semibold text-gray-900 text-right" data-resumen="fecha">—</dd>
                </div>
                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <dt class="text-gray-500">Consultorio</dt>
                    <dd class="text-gray-700 text-right" data-resumen="consultorio">—</dd>
                </div>
            </dl>

            <label class="block text-sm font-medium text-gray-700 mb-1.5">Motivo de la consulta <span class="text-gray-400 font-normal">(opcional)</span></label>
            <textarea name="motivo" rows="3" maxlength="255"
                      placeholder="Cuéntanos brevemente el motivo de tu cita"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('motivo') }}</textarea>

            <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3 text-xs text-teal-700 mt-4">
                Tu cita quedará <strong>asignada directamente</strong> al médico seleccionado en estado <strong>Pendiente</strong>.
            </div>
        </section>

        {{-- Navegación --}}
        <div class="flex items-center justify-between gap-3 mt-6">
            <button type="button" id="boton-atras"
                    class="hidden inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                <span class="rotate-180">@include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-4 h-4'])</span>
                Atrás
            </button>
            <span class="flex-1"></span>
            <a href="{{ route('usuario.citas.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancelar</a>
            <button type="button" id="boton-siguiente"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                Continuar
                @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-4 h-4'])
            </button>
            <button type="submit" id="boton-agendar"
                    class="hidden inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 hover:shadow-md transition">
                @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4'])
                Agendar cita
            </button>
        </div>
    </form>

    <script>
        (function () {
            const form = document.getElementById('form-cita');
            const panels = Array.from(document.querySelectorAll('.panel-paso'));
            const indicadores = Array.from(document.querySelectorAll('.paso-indicador'));
            const botonAtras = document.getElementById('boton-atras');
            const botonSiguiente = document.getElementById('boton-siguiente');
            const botonAgendar = document.getElementById('boton-agendar');

            const inputHora = document.getElementById('hora_inicio');
            const inputFecha = document.getElementById('fecha');
            const cuposContainer = document.getElementById('cupos-container');
            const cuposInfo = document.getElementById('cupos-info');

            const estado = {
                tipoId: null, tipoNombre: '',
                medicoCedula: null, medicoNombre: '', especialidad: '',
                cupo: inputHora.value || '', consultorio: '',
            };

            let paso = {{ (int) $pasoInicial }};

            // ---------- Navegación ----------
            function pintarIndicadores() {
                indicadores.forEach(function (indicador, i) {
                    const circulo = indicador.querySelector('.circulo');
                    const etiqueta = indicador.querySelector('.etiqueta');
                    const linea = indicador.querySelector('.linea');

                    circulo.className = 'circulo flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold border-2 transition';
                    etiqueta.className = 'etiqueta hidden sm:block text-xs font-semibold transition';
                    if (linea) linea.className = 'linea hidden sm:block flex-1 h-px transition';

                    if (i < paso) {
                        circulo.classList.add('bg-teal-600', 'border-teal-600', 'text-white');
                        etiqueta.classList.add('text-teal-700');
                        if (linea) linea.classList.add('bg-teal-500');
                    } else if (i === paso) {
                        circulo.classList.add('border-teal-600', 'text-teal-700', 'bg-white');
                        etiqueta.classList.add('text-gray-900');
                    } else {
                        circulo.classList.add('border-gray-200', 'text-gray-400', 'bg-white');
                        etiqueta.classList.add('text-gray-400');
                        if (linea) linea.classList.add('bg-gray-200');
                    }
                });
            }

            function mostrarPaso(i) {
                paso = i;
                panels.forEach(function (panel, idx) {
                    panel.classList.toggle('hidden', idx !== i);
                });
                botonAtras.classList.toggle('hidden', i === 0);
                botonSiguiente.classList.toggle('hidden', i === panels.length - 1);
                botonAgendar.classList.toggle('hidden', i !== panels.length - 1);
                pintarIndicadores();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function aviso(mensaje) {
                cuposInfo.classList.remove('hidden');
                cuposInfo.textContent = mensaje;
            }

            function validarPaso(i) {
                if (i === 0 && !form.querySelector('input[name="id_tipo_cita"]:checked')) {
                    alert('Selecciona el servicio (tipo de cita) para continuar.');
                    return false;
                }
                if (i === 1 && !form.querySelector('input[name="cedula_medico"]:checked')) {
                    alert('Selecciona el profesional que te atenderá.');
                    return false;
                }
                if (i === 2) {
                    if (!inputFecha.value) {
                        alert('Selecciona la fecha de la cita.');
                        return false;
                    }
                    if (!estado.cupo) {
                        alert('Selecciona un cupo de horario disponible.');
                        return false;
                    }
                }
                return true;
            }

            botonSiguiente.addEventListener('click', function () {
                if (!validarPaso(paso)) return;
                const destino = Math.min(paso + 1, panels.length - 1);
                if (paso === 2) actualizarResumen();
                mostrarPaso(destino);
                if (destino === 2) cargarCupos();
            });

            botonAtras.addEventListener('click', function () {
                mostrarPaso(Math.max(paso - 1, 0));
            });

            // ---------- Paso 1: servicio ----------
            form.querySelectorAll('input[name="id_tipo_cita"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    estado.tipoId = radio.value;
                    estado.tipoNombre = radio.dataset.nombre;
                    estado.cupo = '';
                    inputHora.value = '';
                    // Si cambia el servicio, la duración cambia: recargar cupos.
                    if (paso === 2) cargarCupos();
                });
                if (radio.checked) {
                    estado.tipoId = radio.value;
                    estado.tipoNombre = radio.dataset.nombre;
                }
            });

            // ---------- Paso 2: profesional ----------
            form.querySelectorAll('input[name="cedula_medico"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    estado.medicoCedula = radio.value;
                    estado.medicoNombre = radio.dataset.nombre;
                    estado.especialidad = radio.dataset.especialidad;
                    estado.cupo = '';
                    inputHora.value = '';
                });
                if (radio.checked) {
                    estado.medicoCedula = radio.value;
                    estado.medicoNombre = radio.dataset.nombre;
                    estado.especialidad = radio.dataset.especialidad;
                }
            });

            document.querySelectorAll('.chip-especialidad').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    const filtro = chip.dataset.filtro;
                    document.querySelectorAll('.chip-especialidad').forEach(function (c) {
                        c.classList.remove('activo');
                        c.classList.remove('bg-teal-600', 'text-white', 'border-teal-600');
                        c.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
                    });
                    chip.classList.add('activo', 'bg-teal-600', 'text-white', 'border-teal-600');
                    chip.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');

                    let visibles = 0;
                    document.querySelectorAll('.medico-item').forEach(function (item) {
                        const coincide = filtro === 'todos' || item.dataset.especialidad === filtro;
                        item.classList.toggle('hidden', !coincide);
                        if (coincide) visibles++;
                    });
                    document.getElementById('sin-medicos').classList.toggle('hidden', visibles > 0);
                });
            });

            // ---------- Paso 3: cupos ----------
            inputFecha.addEventListener('change', function () {
                estado.cupo = '';
                inputHora.value = '';
                cargarCupos();
            });

            function cargarCupos() {
                if (!estado.medicoCedula || !estado.tipoId || !inputFecha.value) {
                    cuposContainer.innerHTML = '<p class="text-sm text-gray-400 py-6 text-center">Elige un médico y una fecha para ver los cupos.</p>';
                    return;
                }

                cuposInfo.classList.add('hidden');
                cuposContainer.innerHTML = '<p class="text-sm text-gray-400 py-6 text-center">Cargando cupos disponibles…</p>';

                const url = new URL('{{ route('usuario.citas.cupos') }}', window.location.origin);
                url.searchParams.set('cedula_medico', estado.medicoCedula);
                url.searchParams.set('id_tipo_cita', estado.tipoId);
                url.searchParams.set('fecha', inputFecha.value);

                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        estado.consultorio = data.consultorio || '';
                        renderCupos(data);
                    })
                    .catch(function () {
                        cuposContainer.innerHTML = '<p class="text-sm text-red-500 py-6 text-center">No se pudieron cargar los cupos. Intenta de nuevo.</p>';
                    });
            }

            function renderCupos(data) {
                cuposContainer.innerHTML = '';

                if (!data.cupos || !data.cupos.length) {
                    cuposContainer.innerHTML = '<p class="text-sm text-gray-400 py-6 text-center">'
                        + (data.mensaje || 'No hay cupos disponibles para esa fecha.') + '</p>';
                    return;
                }

                if (data.consultorio) {
                    cuposInfo.classList.remove('hidden');
                    cuposInfo.textContent = 'Consultorio asignado: ' + data.consultorio + ' · duración ' + data.duracion + ' min.';
                }

                const titulo = document.createElement('p');
                titulo.className = 'text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2';
                titulo.textContent = 'Cupos disponibles';
                cuposContainer.appendChild(titulo);

                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-3 sm:grid-cols-5 gap-2';

                data.cupos.forEach(function (c) {
                    const boton = document.createElement('button');
                    boton.type = 'button';
                    boton.textContent = c.hora;
                    boton.dataset.hora = c.hora;
                    boton.className = 'cupo-boton px-2 py-2.5 text-sm font-semibold rounded-lg border transition '
                        + (estado.cupo === c.hora
                            ? 'bg-teal-600 text-white border-teal-600'
                            : 'bg-white text-gray-700 border-gray-300 hover:border-teal-400 hover:text-teal-700');
                    boton.addEventListener('click', function () {
                        seleccionarCupo(boton, c.hora);
                    });
                    grid.appendChild(boton);
                });

                cuposContainer.appendChild(grid);
            }

            function seleccionarCupo(boton, hora) {
                estado.cupo = hora;
                inputHora.value = hora;
                document.querySelectorAll('.cupo-boton').forEach(function (b) {
                    b.className = 'cupo-boton px-2 py-2.5 text-sm font-semibold rounded-lg border transition bg-white text-gray-700 border-gray-300 hover:border-teal-400 hover:text-teal-700';
                });
                boton.className = 'cupo-boton px-2 py-2.5 text-sm font-semibold rounded-lg border transition bg-teal-600 text-white border-teal-600';
            }

            // ---------- Paso 4: resumen ----------
            function formatearFecha(iso) {
                if (!iso) return '—';
                const partes = iso.split('-');
                return partes[2] + '/' + partes[1] + '/' + partes[0];
            }

            function actualizarResumen() {
                form.querySelector('[data-resumen="servicio"]').textContent = estado.tipoNombre || '—';
                form.querySelector('[data-resumen="medico"]').textContent = estado.medicoNombre || '—';
                form.querySelector('[data-resumen="especialidad"]').textContent = estado.especialidad || '—';
                form.querySelector('[data-resumen="fecha"]').textContent = formatearFecha(inputFecha.value) + (estado.cupo ? ' · ' + estado.cupo : '');
                form.querySelector('[data-resumen="consultorio"]').textContent = estado.consultorio || '—';
            }

            // ---------- Estado inicial ----------
            pintarIndicadores();
            mostrarPaso(paso);
            if (paso >= 2) cargarCupos();
        })();
    </script>

@endsection
