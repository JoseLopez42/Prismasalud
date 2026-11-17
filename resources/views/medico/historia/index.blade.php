@extends('medico.layout')

@section('titulo', 'Historia médica')
@section('encabezado', 'Historia médica del paciente')

@section('contenido')

    {{-- ===================== AVISOS ===================== --}}
    @if (session('exito'))
        <div id="toast-exito"
             class="fixed top-4 right-4 z-[70] flex items-center gap-3 bg-white border border-emerald-200 shadow-xl rounded-xl pl-4 pr-3 py-3 max-w-sm">
            <span class="w-8 h-8 rounded-full bg-emerald-1000 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </span>
            <p class="text-sm font-medium text-gray-800">{{ session('exito') }}</p>
            <button type="button" onclick="this.closest('#toast-exito').remove()" class="ml-2 text-gray-400 hover:text-gray-600 flex-shrink-0" aria-label="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 text-sm shadow-sm">
            <p class="font-semibold mb-1 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                No se pudo guardar, revisa lo siguiente:
            </p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===================== BUSCADOR ===================== --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </span>
            <div>
                <h2 class="font-bold text-gray-900 leading-tight">Buscar paciente</h2>
                <p class="text-xs text-gray-500">Ingresa la cédula del paciente para abrir su historia clínica</p>
            </div>
        </div>
        <form method="GET" action="{{ route('medico.historia.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" inputmode="numeric" pattern="[0-9]*" name="cedula" value="{{ $cedulaBuscada }}"
                       placeholder="Ej: 1065581109" required
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
            </div>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 hover:shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar paciente
            </button>
        </form>
    </div>

    @if ($cedulaBuscada !== '' && !$paciente)
        <div class="bg-white border border-amber-200 rounded-2xl shadow-sm px-6 py-12 text-center mb-6">
            <span class="inline-flex w-14 h-14 rounded-full bg-amber-50 text-amber-500 items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </span>
            <p class="font-semibold text-gray-800 mb-1">Paciente no encontrado</p>
            <p class="text-sm text-gray-500">No existe ningún paciente registrado con la cédula <strong>{{ $cedulaBuscada }}</strong>.</p>
        </div>
    @endif

    @if ($paciente)
        @php
            $nombrePaciente = trim($paciente->nombres . ' ' . $paciente->apellidos);
            $iniciales = strtoupper(substr($paciente->nombres, 0, 1) . substr($paciente->apellidos, 0, 1));
        @endphp

        {{-- ===================== BARRA DEL PACIENTE ===================== --}}
        <div class="sticky top-0 z-30 -mx-4 md:-mx-8 px-4 md:px-8 py-4 mb-6 bg-white/95 backdrop-blur border-b border-emerald-100 shadow-[0_1px_10px_rgba(0,0,0,0.04)]">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-700 text-white flex items-center justify-center text-lg font-bold flex-shrink-0 shadow-sm">
                        {{ $iniciales }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-lg font-bold text-gray-900 truncate">{{ $nombrePaciente }}</p>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Paciente activo
                            </span>
                            @if ($idCita)
                                <span class="inline-flex items-center text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-100 px-2 py-0.5 rounded-full">
                                    Atendiendo cita #{{ $idCita }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-x-3 gap-y-1 flex-wrap text-xs text-gray-500 mt-0.5">
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                </svg>
                                {{ $paciente->tipoDocumento->nombre ?? 'C.C.' }} {{ $paciente->cedula }}
                            </span>
                            @if ($paciente->edad)
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $paciente->edad }} años
                                </span>
                            @endif
                            @if ($paciente->telefono)
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                    {{ $paciente->telefono }}
                                </span>
                            @endif
                            @if ($paciente->correo)
                                <span class="inline-flex items-center gap-1 truncate">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    {{ $paciente->correo }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 lg:flex lg:items-center">
                    <div class="text-center bg-teal-50 border border-teal-100 rounded-xl px-4 py-2 min-w-[92px]">
                        <p class="text-xl font-bold text-teal-700 leading-none">{{ $historias->count() }}</p>
                        <p class="text-[11px] font-semibold text-teal-600/80 uppercase tracking-wide mt-1">Historias</p>
                    </div>
                    <div class="text-center bg-sky-50 border border-sky-100 rounded-xl px-4 py-2 min-w-[92px]">
                        <p class="text-xl font-bold text-sky-700 leading-none">{{ $diagnosticos->count() }}</p>
                        <p class="text-[11px] font-semibold text-sky-600/80 uppercase tracking-wide mt-1">Diagnósticos</p>
                    </div>
                    <div class="text-center bg-rose-50 border border-rose-100 rounded-xl px-4 py-2 min-w-[92px]">
                        <p class="text-xl font-bold text-rose-700 leading-none">{{ $enfermedades->count() }}</p>
                        <p class="text-[11px] font-semibold text-rose-600/80 uppercase tracking-wide mt-1">Enfermedades</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== PESTAÑAS ===================== --}}
        @php $tabInicial = old('_tab', 'historia'); @endphp

        <div class="flex flex-wrap gap-2 mb-5" id="tabs-historia">
            <button type="button" data-tab="historia"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition">
                @include('admin.partials.icono', ['icono' => 'historia', 'clase' => 'w-4 h-4'])
                Historia clínica
                <span class="tab-contador text-xs rounded-full px-2 py-0.5">{{ $historias->count() }}</span>
            </button>
            <button type="button" data-tab="diagnostico"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition">
                @include('admin.partials.icono', ['icono' => 'lista', 'clase' => 'w-4 h-4'])
                Diagnósticos
                <span class="tab-contador text-xs rounded-full px-2 py-0.5">{{ $diagnosticos->count() }}</span>
            </button>
            <button type="button" data-tab="enfermedad"
                    class="tab-boton inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition">
                @include('admin.partials.icono', ['icono' => 'eps', 'clase' => 'w-4 h-4'])
                Enfermedades
                <span class="tab-contador text-xs rounded-full px-2 py-0.5">{{ $enfermedades->count() }}</span>
            </button>
        </div>

        {{-- ===================== HISTORIA CLÍNICA ===================== --}}
        <section id="panel-historia" class="tab-panel">
            <div class="grid xl:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] gap-6 items-start">

                {{-- Formulario: nota de evolución --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-r from-teal-600 to-emerald-700">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nueva historia clínica
                        </h3>
                        <p class="text-xs text-teal-100 mt-0.5">Registra la atención médica del paciente</p>
                    </div>

                    <form method="POST" action="{{ route('medico.historia.store') }}" class="p-5 space-y-5">
                        @csrf
                        <input type="hidden" name="_tab" value="historia">
                        <input type="hidden" name="cedula_paciente" value="{{ $paciente->cedula }}">
                        @if ($idCita)
                            <input type="hidden" name="id_cita" value="{{ $idCita }}">
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Fecha de atención *</label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                        </div>

                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/60">
                            <p class="text-xs font-bold text-teal-800 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">1</span>
                                Motivo de consulta
                            </p>
                            <textarea name="motivo_consulta" rows="2" placeholder="Ej: Cefalea intensa de 2 días de evolución, acompañada de náusea y fotofobia…"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('motivo_consulta') }}</textarea>
                        </div>

                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/60">
                            <p class="text-xs font-bold text-teal-800 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">2</span>
                                Antecedentes
                            </p>
                            <textarea name="antecedentes" rows="3" placeholder="Personales, familiares, quirúrgicos, alergias, medicación actual…"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('antecedentes') }}</textarea>
                            <p class="text-[11px] text-gray-400 mt-1.5">Incluye antecedentes relevantes para la atención actual.</p>
                        </div>

                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/60">
                            <p class="text-xs font-bold text-teal-800 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">3</span>
                                Examen físico
                            </p>
                            <textarea name="examen_fisico" rows="3" placeholder="Ej: TA 120/80 mmHg, FC 78 lpm, FR 16 rpm, T° 36.7 °C. Paciente consciente, orientado, hidratado…"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('examen_fisico') }}</textarea>
                            <p class="text-[11px] text-gray-400 mt-1.5">Registra constantes vitales y hallazgos del examen por sistemas.</p>
                        </div>

                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/60">
                            <p class="text-xs font-bold text-teal-800 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">4</span>
                                Observaciones y plan
                            </p>
                            <textarea name="observaciones" rows="3" placeholder="Ej: Se indica manejo sintomático, hidratación y control en 5 días…"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('observaciones') }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 text-white font-semibold py-3 rounded-xl hover:bg-teal-700 hover:shadow-md transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Guardar historia clínica
                        </button>
                    </form>
                </div>

                {{-- Historial en línea de tiempo --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="flex items-center justify-between gap-2 mb-5">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Historial de atenciones
                        </h3>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $historias->count() }} registros</span>
                    </div>

                    @if ($historias->isEmpty())
                        <div class="text-center py-14">
                            <span class="inline-flex w-14 h-14 rounded-full bg-gray-50 text-gray-300 items-center justify-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </span>
                            <p class="font-semibold text-gray-600 text-sm">Sin historias clínicas</p>
                            <p class="text-xs text-gray-400 mt-1">Las atenciones que registres aparecerán aquí en orden cronológico.</p>
                        </div>
                    @else
                        <div class="relative space-y-5 before:absolute before:left-[7px] before:top-3 before:bottom-3 before:w-px before:bg-teal-100">
                            @foreach ($historias as $historia)
                                @php $esAutor = (int) ($_SESSION['medico_cedula'] ?? 0) === (int) $historia->cedula_medico; @endphp
                                <article class="relative pl-8">
                                    <span class="absolute left-0 top-1.5 w-[15px] h-[15px] rounded-full bg-teal-500 ring-4 ring-teal-50 border-2 border-white"></span>

                                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:border-teal-200 transition">
                                        <header class="flex items-start justify-between gap-3 flex-wrap mb-3">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $historia->fecha->format('d/m/Y') }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    Dr(a). {{ $historia->medico->nombres ?? '' }} {{ $historia->medico->apellidos ?? '' }}
                                                    @if ($historia->medico && $historia->medico->especialidad)
                                                        <span class="text-teal-700 font-medium">· {{ $historia->medico->especialidad->nombre }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if ($historia->cita)
                                                    <span class="text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-100 px-2 py-0.5 rounded-full">Cita #{{ $historia->cita->id }}</span>
                                                @endif
                                                @if ($esAutor)
                                                    @php
                                                        $registroHistoria = [
                                                            'fecha' => $historia->fecha->format('Y-m-d'),
                                                            'motivo_consulta' => $historia->motivo_consulta,
                                                            'antecedentes' => $historia->antecedentes,
                                                            'examen_fisico' => $historia->examen_fisico,
                                                            'observaciones' => $historia->observaciones,
                                                        ];
                                                    @endphp
                                                    <button type="button"
                                                            data-editar="historia" data-id="{{ $historia->id }}"
                                                            data-action="{{ route('medico.historia.update', $historia->id) }}"
                                                            data-record="@json($registroHistoria)"
                                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-lg hover:bg-teal-100 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                        Editar
                                                    </button>
                                                @endif
                                            </div>
                                        </header>

                                        <div class="space-y-2">
                                            @foreach ([
                                                'Motivo de consulta' => $historia->motivo_consulta,
                                                'Antecedentes'       => $historia->antecedentes,
                                                'Examen físico'      => $historia->examen_fisico,
                                                'Observaciones'      => $historia->observaciones,
                                            ] as $etiqueta => $valor)
                                                @if ($valor)
                                                    <div class="bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
                                                        <p class="text-[10px] font-bold text-teal-700 uppercase tracking-wider">{{ $etiqueta }}</p>
                                                        <p class="text-sm text-gray-700 mt-0.5 whitespace-pre-line">{{ $valor }}</p>
                                                    </div>
                                                @endif
                                            @endforeach

                                            @if (!$historia->motivo_consulta && !$historia->antecedentes && !$historia->examen_fisico && !$historia->observaciones)
                                                <p class="text-xs text-gray-400 italic">Sin detalles registrados en esta atención.</p>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ===================== DIAGNÓSTICO ===================== --}}
        <section id="panel-diagnostico" class="tab-panel hidden">
            <div class="grid xl:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] gap-6 items-start">

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-r from-sky-600 to-blue-700">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nuevo diagnóstico
                        </h3>
                        <p class="text-xs text-sky-100 mt-0.5">Registra el diagnóstico del paciente</p>
                    </div>

                    <form method="POST" action="{{ route('medico.diagnostico.store') }}" class="p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="diagnostico">
                        <input type="hidden" name="cedula_paciente" value="{{ $paciente->cedula }}">

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Fecha *</label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Descripción *</label>
                            <input type="text" name="descripcion" value="{{ old('descripcion') }}" maxlength="255" required
                                   placeholder="Ej: Faringitis aguda"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Tipo de diagnóstico</label>
                            <select name="tipo"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                <option value="">Selecciona...</option>
                                @foreach (['Presuntivo', 'Confirmado', 'Definitivo'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Historia clínica <span class="text-gray-400 font-normal normal-case">(opcional)</span></label>
                            <select name="id_historia_clinica"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                <option value="">Sin asociar</option>
                                @foreach ($historias as $historia)
                                    <option value="{{ $historia->id }}" {{ old('id_historia_clinica') == $historia->id ? 'selected' : '' }}>
                                        {{ $historia->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($historia->motivo_consulta ?: 'Sin motivo', 40) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1.5">Asócialo a una historia para integrar la evolución del paciente.</p>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-sky-600 text-white font-semibold py-3 rounded-xl hover:bg-sky-700 hover:shadow-md transition">
                            Guardar diagnóstico
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="flex items-center justify-between gap-2 mb-5">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                            </svg>
                            Diagnósticos registrados
                        </h3>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $diagnosticos->count() }}</span>
                    </div>

                    @if ($diagnosticos->isEmpty())
                        <div class="text-center py-14">
                            <span class="inline-flex w-14 h-14 rounded-full bg-gray-50 text-gray-300 items-center justify-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                                </svg>
                            </span>
                            <p class="font-semibold text-gray-600 text-sm">Sin diagnósticos registrados</p>
                            <p class="text-xs text-gray-400 mt-1">Los diagnósticos del paciente aparecerán en esta lista.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($diagnosticos as $diagnostico)
                                @php
                                    $esAutor = (int) ($_SESSION['medico_cedula'] ?? 0) === (int) $diagnostico->cedula_medico;
                                    $coloresTipo = [
                                        'Presuntivo' => 'text-amber-700 bg-amber-50 border-amber-200',
                                        'Confirmado' => 'text-sky-700 bg-sky-50 border-sky-200',
                                        'Definitivo' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                                    ];
                                    $colorTipo = $coloresTipo[$diagnostico->tipo] ?? 'text-gray-600 bg-gray-50 border-gray-200';
                                @endphp
                                <div class="border border-gray-200 rounded-xl p-4 shadow-sm hover:border-sky-200 transition">
                                    <div class="flex items-start justify-between gap-3 flex-wrap">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-sm font-bold text-gray-900">{{ $diagnostico->descripcion }}</p>
                                                @if ($diagnostico->tipo)
                                                    <span class="text-[11px] font-semibold border px-2 py-0.5 rounded-full {{ $colorTipo }}">{{ $diagnostico->tipo }}</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $diagnostico->fecha->format('d/m/Y') }}
                                                · Dr(a). {{ $diagnostico->medico->nombres ?? '' }} {{ $diagnostico->medico->apellidos ?? '' }}
                                                @if ($diagnostico->medico && $diagnostico->medico->especialidad)
                                                    <span class="text-sky-700 font-medium">· {{ $diagnostico->medico->especialidad->nombre }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        @if ($esAutor)
                                            @php
                                                $registroDiagnostico = [
                                                    'fecha' => $diagnostico->fecha->format('Y-m-d'),
                                                    'descripcion' => $diagnostico->descripcion,
                                                    'tipo' => $diagnostico->tipo,
                                                    'id_historia_clinica' => $diagnostico->id_historia_clinica,
                                                ];
                                            @endphp
                                            <button type="button"
                                                    data-editar="diagnostico" data-id="{{ $diagnostico->id }}"
                                                    data-action="{{ route('medico.diagnostico.update', $diagnostico->id) }}"
                                                    data-record="@json($registroDiagnostico)"
                                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-lg hover:bg-teal-100 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Editar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ===================== ENFERMEDAD ===================== --}}
        <section id="panel-enfermedad" class="tab-panel hidden">
            <div class="grid xl:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] gap-6 items-start">

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-r from-rose-600 to-red-700">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nueva enfermedad
                        </h3>
                        <p class="text-xs text-rose-100 mt-0.5">Registra patologías del paciente</p>
                    </div>

                    <form method="POST" action="{{ route('medico.enfermedad.store') }}" class="p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="_tab" value="enfermedad">
                        <input type="hidden" name="cedula_paciente" value="{{ $paciente->cedula }}">

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Fecha *</label>
                            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Nombre de la enfermedad *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" maxlength="150" required
                                   placeholder="Ej: Hipertensión arterial esencial"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Descripción</label>
                            <textarea name="descripcion" rows="3" placeholder="Ej: Diagnóstico de HTA en control, última TA 130/85 mmHg…"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">{{ old('descripcion') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Historia clínica <span class="text-gray-400 font-normal normal-case">(opcional)</span></label>
                            <select name="id_historia_clinica"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition">
                                <option value="">Sin asociar</option>
                                @foreach ($historias as $historia)
                                    <option value="{{ $historia->id }}" {{ old('id_historia_clinica') == $historia->id ? 'selected' : '' }}>
                                        {{ $historia->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($historia->motivo_consulta ?: 'Sin motivo', 40) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-rose-600 text-white font-semibold py-3 rounded-xl hover:bg-rose-700 hover:shadow-md transition">
                            Guardar enfermedad
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                    <div class="flex items-center justify-between gap-2 mb-5">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            Enfermedades registradas
                        </h3>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $enfermedades->count() }}</span>
                    </div>

                    @if ($enfermedades->isEmpty())
                        <div class="text-center py-14">
                            <span class="inline-flex w-14 h-14 rounded-full bg-gray-50 text-gray-300 items-center justify-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </span>
                            <p class="font-semibold text-gray-600 text-sm">Sin enfermedades registradas</p>
                            <p class="text-xs text-gray-400 mt-1">Las patologías del paciente aparecerán en esta lista.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($enfermedades as $enfermedad)
                                @php $esAutor = (int) ($_SESSION['medico_cedula'] ?? 0) === (int) $enfermedad->cedula_medico; @endphp
                                <div class="border border-gray-200 rounded-xl p-4 shadow-sm hover:border-rose-200 transition">
                                    <div class="flex items-start justify-between gap-3 flex-wrap">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-sm font-bold text-gray-900">{{ $enfermedad->nombre }}</p>
                                                <span class="text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-full">{{ $enfermedad->fecha->format('d/m/Y') }}</span>
                                            </div>
                                            @if ($enfermedad->descripcion)
                                                <p class="text-xs text-gray-600 mt-1.5 whitespace-pre-line">{{ $enfermedad->descripcion }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">
                                                Dr(a). {{ $enfermedad->medico->nombres ?? '' }} {{ $enfermedad->medico->apellidos ?? '' }}
                                                @if ($enfermedad->medico && $enfermedad->medico->especialidad)
                                                    <span class="text-rose-700 font-medium">· {{ $enfermedad->medico->especialidad->nombre }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        @if ($esAutor)
                                            @php
                                                $registroEnfermedad = [
                                                    'fecha' => $enfermedad->fecha->format('Y-m-d'),
                                                    'nombre' => $enfermedad->nombre,
                                                    'descripcion' => $enfermedad->descripcion,
                                                    'id_historia_clinica' => $enfermedad->id_historia_clinica,
                                                ];
                                            @endphp
                                            <button type="button"
                                                    data-editar="enfermedad" data-id="{{ $enfermedad->id }}"
                                                    data-action="{{ route('medico.enfermedad.update', $enfermedad->id) }}"
                                                    data-record="@json($registroEnfermedad)"
                                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-lg hover:bg-teal-100 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Editar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ===================== MODAL DE EDICIÓN ===================== --}}
        <div id="modal-editar" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-cerrar-modal></div>
            <div class="relative mx-auto mt-[8vh] w-[94%] max-w-2xl bg-white rounded-2xl shadow-2xl max-h-[84vh] flex flex-col overflow-hidden">
                <header class="flex items-center justify-between gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" style="width:1.125rem;height:1.125rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </span>
                        <h3 id="modal-titulo" class="font-bold text-gray-900">Editar registro</h3>
                    </div>
                    <button type="button" data-cerrar-modal class="text-gray-400 hover:text-gray-600 p-1" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </header>

                <form id="modal-form" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_tab" id="modal-tab">
                    <input type="hidden" name="_edit" id="modal-edit">
                    <div id="modal-campos" class="px-6 py-5 space-y-4"></div>
                    <footer class="flex gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-teal-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-teal-700 transition">
                            Guardar cambios
                        </button>
                        <button type="button" data-cerrar-modal class="px-5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                    </footer>
                </form>
            </div>
        </div>

        {{-- Valores antiguos para reabrir el modal tras un error de validación --}}
        <script type="application/json" id="datos-edicion">@json(['edit' => old('_edit'), 'valores' => old() ?? []])</script>
    @endif

@endsection

@push('scripts')
    @if ($paciente)
    @php
        $historiasOptions = $historias->map(function ($h) {
            return [
                'id' => $h->id,
                'texto' => $h->fecha->format('d/m/Y') . ' — ' . \Illuminate\Support\Str::limit($h->motivo_consulta ?: 'Sin motivo', 40),
            ];
        })->values();
    @endphp
    <script>
        (function () {
            // ---------- Toast de éxito ----------
            var toast = document.getElementById('toast-exito');
            if (toast) {
                setTimeout(function () {
                    toast.style.transition = 'opacity .4s ease, transform .4s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-8px)';
                    setTimeout(function () { toast.remove(); }, 400);
                }, 4500);
            }

            // ---------- Pestañas ----------
            var botones = document.querySelectorAll('#tabs-historia .tab-boton');
            var paneles = document.querySelectorAll('.tab-panel');

            var colores = {
                historia:    { activo: ['bg-teal-600', 'text-white', 'border-teal-600'],  inactivo: ['bg-white', 'text-gray-600', 'border-gray-200'], contador: ['bg-white/20', 'text-white'] },
                diagnostico: { activo: ['bg-sky-600', 'text-white', 'border-sky-600'],   inactivo: ['bg-white', 'text-gray-600', 'border-gray-200'], contador: ['bg-white/20', 'text-white'] },
                enfermedad:  { activo: ['bg-rose-600', 'text-white', 'border-rose-600'], inactivo: ['bg-white', 'text-gray-600', 'border-gray-200'], contador: ['bg-white/20', 'text-white'] }
            };
            var pasivas = ['bg-white', 'text-gray-600', 'border-gray-200'];
            var contadorPasivo = ['bg-gray-100', 'text-gray-500'];

            function activarTab(nombre) {
                botones.forEach(function (b) {
                    var clave = b.getAttribute('data-tab');
                    var activo = clave === nombre;
                    var cfg = colores[clave] || colores.historia;

                    pasivas.concat(contadorPasivo).forEach(function (c) { b.classList.remove(c); });
                    cfg.activo.concat(cfg.contador).forEach(function (c) { b.classList.remove(c); });

                    if (activo) {
                        cfg.activo.forEach(function (c) { b.classList.add(c); });
                        var contador = b.querySelector('.tab-contador');
                        if (contador) { cfg.contador.forEach(function (c) { contador.classList.add(c); }); }
                    } else {
                        pasivas.forEach(function (c) { b.classList.add(c); });
                        var contador = b.querySelector('.tab-contador');
                        if (contador) { contadorPasivo.forEach(function (c) { contador.classList.add(c); }); }
                    }
                });
                paneles.forEach(function (p) {
                    p.classList.toggle('hidden', p.id !== 'panel-' + nombre);
                });
            }

            botones.forEach(function (b) {
                b.addEventListener('click', function () { activarTab(b.getAttribute('data-tab')); });
            });

            activarTab('{{ $tabInicial }}');

            // ---------- Modal de edición ----------
            var modal = document.getElementById('modal-editar');
            var modalForm = document.getElementById('modal-form');
            var modalCampos = document.getElementById('modal-campos');
            var modalTitulo = document.getElementById('modal-titulo');
            var modalTab = document.getElementById('modal-tab');
            var modalEdit = document.getElementById('modal-edit');

            var CONFIG = {
                historia: {
                    titulo: 'Editar historia clínica',
                    tab: 'historia',
                    campos: [
                        { name: 'fecha', label: 'Fecha de atención *', type: 'date', required: true },
                        { name: 'motivo_consulta', label: 'Motivo de consulta', type: 'textarea', rows: 2 },
                        { name: 'antecedentes', label: 'Antecedentes', type: 'textarea', rows: 3 },
                        { name: 'examen_fisico', label: 'Examen físico', type: 'textarea', rows: 3 },
                        { name: 'observaciones', label: 'Observaciones y plan', type: 'textarea', rows: 3 }
                    ]
                },
                diagnostico: {
                    titulo: 'Editar diagnóstico',
                    tab: 'diagnostico',
                    campos: [
                        { name: 'fecha', label: 'Fecha *', type: 'date', required: true },
                        { name: 'descripcion', label: 'Descripción *', type: 'text', required: true, maxlength: 255 },
                        { name: 'tipo', label: 'Tipo de diagnóstico', type: 'select', opciones: ['Presuntivo', 'Confirmado', 'Definitivo'] },
                        { name: 'id_historia_clinica', label: 'Historia clínica (opcional)', type: 'select-historias' }
                    ]
                },
                enfermedad: {
                    titulo: 'Editar enfermedad',
                    tab: 'enfermedad',
                    campos: [
                        { name: 'fecha', label: 'Fecha *', type: 'date', required: true },
                        { name: 'nombre', label: 'Nombre de la enfermedad *', type: 'text', required: true, maxlength: 150 },
                        { name: 'descripcion', label: 'Descripción', type: 'textarea', rows: 3 },
                        { name: 'id_historia_clinica', label: 'Historia clínica (opcional)', type: 'select-historias' }
                    ]
                }
            };

            var historiasOptions = @json($historiasOptions);

            function crearCampo(campo, valor) {
                var wrap = document.createElement('div');
                var label = document.createElement('label');
                label.className = 'block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5';
                label.textContent = campo.label;
                wrap.appendChild(label);

                var input;
                if (campo.type === 'textarea') {
                    input = document.createElement('textarea');
                    input.rows = campo.rows || 3;
                } else if (campo.type === 'select') {
                    input = document.createElement('select');
                    var vacio = document.createElement('option');
                    vacio.value = '';
                    vacio.textContent = 'Selecciona...';
                    input.appendChild(vacio);
                    campo.opciones.forEach(function (op) {
                        var o = document.createElement('option');
                        o.value = op;
                        o.textContent = op;
                        input.appendChild(o);
                    });
                } else if (campo.type === 'select-historias') {
                    input = document.createElement('select');
                    var vacio2 = document.createElement('option');
                    vacio2.value = '';
                    vacio2.textContent = 'Sin asociar';
                    input.appendChild(vacio2);
                    historiasOptions.forEach(function (h) {
                        var o = document.createElement('option');
                        o.value = h.id;
                        o.textContent = h.texto;
                        input.appendChild(o);
                    });
                } else {
                    input = document.createElement('input');
                    input.type = campo.type || 'text';
                }

                input.name = campo.name;
                if (campo.maxlength) { input.maxLength = campo.maxlength; }
                if (campo.required) { input.required = true; }
                input.value = valor == null ? '' : valor;
                input.className = 'w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition';
                wrap.appendChild(input);
                return wrap;
            }

            function abrirModal(tipo, id, action, record) {
                var cfg = CONFIG[tipo];
                if (!cfg) { return; }

                modalTitulo.textContent = cfg.titulo;
                modalTab.value = cfg.tab;
                modalEdit.value = id;
                modalForm.action = action;
                modalCampos.innerHTML = '';

                var datos = {};
                try { datos = JSON.parse(record || '{}'); } catch (e) { datos = {}; }

                // Si hay old() tras un error de validación, tiene prioridad
                var contexto = window.__edicionContexto || {};
                if (contexto.edit && String(contexto.edit) === String(id)) {
                    cfg.campos.forEach(function (campo) {
                        if (contexto.valores && contexto.valores[campo.name] !== undefined) {
                            datos[campo.name] = contexto.valores[campo.name];
                        }
                    });
                }

                cfg.campos.forEach(function (campo) {
                    modalCampos.appendChild(crearCampo(campo, datos[campo.name]));
                });

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                var primer = modalCampos.querySelector('input, textarea, select');
                if (primer) { primer.focus(); }
            }

            function cerrarModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-editar]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    abrirModal(
                        btn.getAttribute('data-editar'),
                        btn.getAttribute('data-id'),
                        btn.getAttribute('data-action'),
                        btn.getAttribute('data-record')
                    );
                });
            });

            modal.querySelectorAll('[data-cerrar-modal]').forEach(function (el) {
                el.addEventListener('click', cerrarModal);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) { cerrarModal(); }
            });

            // Contexto de old() para reabrir el modal con errores de validación
            try {
                var nodoEdicion = document.getElementById('datos-edicion');
                window.__edicionContexto = nodoEdicion ? JSON.parse(nodoEdicion.textContent) : {};
            } catch (e) {
                window.__edicionContexto = {};
            }

            (function () {
                var ctx = window.__edicionContexto;
                if (!ctx || !ctx.edit) { return; }
                var btn = document.querySelector('[data-editar][data-id="' + ctx.edit + '"]');
                if (btn) { btn.click(); }
            })();
        })();
    </script>
    @endif
@endpush
