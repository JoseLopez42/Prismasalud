@extends('admin.layout')

@section('titulo', 'Nuevo horario')
@section('encabezado', 'Médicos y Consultorios · Nuevo horario')

@push('estilos')
<style>
    @keyframes aparecer {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .alerta-animada { animation: aparecer .25s ease; }

    @keyframes sacudir {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .campo-invalido { animation: sacudir .3s ease; border-color: #ef4444 !important; }

    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: girar .6s linear infinite;
    }
    @keyframes girar { to { transform: rotate(360deg); } }
</style>
@endpush

@section('contenido')

    <a href="{{ route('horario_medico.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a horarios
    </a>

    <div class="max-w-lg mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Nuevo horario</p>
                        <p class="text-xs text-white/70">Fecha y hora de atención de un médico</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-horario" method="POST" action="{{ route('horario_medico.store') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Médico *</label>
                        <select name="cedula_medico"
                                class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                required>
                            <option value="" disabled {{ old('cedula_medico') ? '' : 'selected' }}>Selecciona un médico...</option>
                            @foreach ($medicos as $medico)
                                <option value="{{ $medico->cedula }}" {{ old('cedula_medico') == $medico->cedula ? 'selected' : '' }}>
                                    {{ $medico->apellidos }}, {{ $medico->nombres }} ({{ $medico->especialidad->nombre ?? 'sin especialidad' }})
                                </option>
                            @endforeach
                        </select>
                        @if ($medicos->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">Primero debes crear al menos un médico activo.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Consultorio *</label>
                        <select name="id_consultorio"
                                class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                required>
                            <option value="" disabled {{ old('id_consultorio') ? '' : 'selected' }}>Selecciona un consultorio...</option>
                            @foreach ($consultorios as $consultorio)
                                <option value="{{ $consultorio->id }}" {{ old('id_consultorio') == $consultorio->id ? 'selected' : '' }}>
                                    {{ $consultorio->nombre }}{{ $consultorio->sede ? ' · ' . $consultorio->sede->nombre_sede : '' }}
                                </option>
                            @endforeach
                        </select>
                        @if ($consultorios->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">Primero debes crear al menos un consultorio activo.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de atención *</label>
                        <input type="date" name="fecha" value="{{ old('fecha') }}"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                               required>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora de inicio *</label>
                            <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora de finalización *</label>
                            <input type="time" name="hora_fin" value="{{ old('hora_fin') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('horario_medico.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-horario"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-horario" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Crear horario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formHorario = document.getElementById('form-horario');
        const botonGuardarHorario = document.getElementById('boton-guardar-horario');
        const textoBotonHorario = document.getElementById('texto-boton-horario');

        formHorario.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formHorario.querySelectorAll('.campo-form'))
                .filter(campo => !campo.checkValidity());

            if (camposInvalidos.length > 0) {
                evento.preventDefault();
                camposInvalidos.forEach(campo => {
                    campo.classList.add('campo-invalido');
                    campo.addEventListener('animationend', () => campo.classList.remove('campo-invalido'), { once: true });
                });
                camposInvalidos[0].focus();
                return;
            }

            const inicio = formHorario.hora_inicio.value;
            const fin = formHorario.hora_fin.value;
            if (inicio && fin && fin <= inicio) {
                evento.preventDefault();
                const campoFin = formHorario.hora_fin;
                campoFin.classList.add('campo-invalido');
                campoFin.addEventListener('animationend', () => campoFin.classList.remove('campo-invalido'), { once: true });
                campoFin.focus();
                return;
            }

            botonGuardarHorario.disabled = true;
            textoBotonHorario.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection
