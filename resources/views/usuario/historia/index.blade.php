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

    @php
        $estilosBoton = [
            'teal'   => 'text-teal-700 bg-teal-50 border-teal-100 hover:bg-teal-100',
            'indigo' => 'text-indigo-700 bg-indigo-50 border-indigo-100 hover:bg-indigo-100',
            'rose'   => 'text-rose-700 bg-rose-50 border-rose-100 hover:bg-rose-100',
            'amber'  => 'text-amber-700 bg-amber-50 border-amber-100 hover:bg-amber-100',
        ];
    @endphp

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
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-gray-900">{{ $item->fecha->format('d/m/Y') }}</span>
                                        <span class="text-[11px] font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-full">Atención médica</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Dr(a). {{ $item->medico->nombres ?? '' }} {{ $item->medico->apellidos ?? '' }}
                                        @if ($item->medico && $item->medico->especialidad)
                                            <span class="text-teal-700 font-medium">· {{ $item->medico->especialidad->nombre }}</span>
                                        @endif
                                    </p>
                                    @if ($item->motivo_consulta)
                                        <p class="text-xs text-gray-600 mt-1.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($item->motivo_consulta, 110) }}</p>
                                    @endif
                                </div>
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
                            @if ($item->descripcion)<p class="text-xs text-gray-600 mt-1.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($item->descripcion, 110) }}</p>@endif
                        @else
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->descripcion }}</span>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $item->fecha->format('d/m/Y') }}</span>
                            </div>
                            @if ($item->tipoProcedimiento)<span class="inline-block text-xs text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full mt-2">{{ $item->tipoProcedimiento->nombre }}</span>@endif
                            @if ($item->observaciones)<p class="text-xs text-gray-600 mt-1.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($item->observaciones, 110) }}</p>@endif
                        @endif

                        <button type="button" data-ver-detalle="{{ $bloque['tipo'] }}-{{ $item->id }}"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 text-xs font-semibold border px-3 py-2 rounded-lg transition {{ $estilosBoton[$bloque['color']] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Ver detalle
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">Sin registros.</p>
                @endforelse
            </div>
        @endforeach
    </div>

    {{-- ============================================================
         MODALES DE DETALLE
         ============================================================ --}}

    {{-- Historias clínicas --}}
    @foreach ($historias as $historia)
        <div id="modal-detalle-historia-{{ $historia->id }}" class="modal-detalle fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-cerrar-modal></div>
            <div class="relative mx-auto mt-[8vh] w-[94%] max-w-2xl bg-white rounded-2xl shadow-2xl max-h-[84vh] flex flex-col overflow-hidden">
                <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-teal-600 to-emerald-700 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-white/15 text-white flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-bold text-white truncate">Historia clínica del {{ $historia->fecha->format('d/m/Y') }}</h3>
                            <p class="text-xs text-teal-100 truncate mt-0.5">
                                Dr(a). {{ $historia->medico->nombres ?? '' }} {{ $historia->medico->apellidos ?? '' }}
                                @if ($historia->medico && $historia->medico->especialidad) · {{ $historia->medico->especialidad->nombre }} @endif
                            </p>
                        </div>
                    </div>
                    <button type="button" data-cerrar-modal class="text-white/80 hover:text-white p-1 flex-shrink-0" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </header>

                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    @if ($historia->cita)
                        <span class="inline-flex items-center text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-full">Registrado en la cita #{{ $historia->cita->id }}</span>
                    @endif
                    @foreach (['Motivo de consulta' => $historia->motivo_consulta, 'Antecedentes' => $historia->antecedentes, 'Examen físico' => $historia->examen_fisico, 'Observaciones y plan' => $historia->observaciones] as $etiqueta => $valor)
                        @if ($valor)
                            <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                                <p class="text-[10px] font-bold text-teal-700 uppercase tracking-wider mb-1">{{ $etiqueta }}</p>
                                <p class="text-sm text-gray-800 whitespace-pre-line">{{ $valor }}</p>
                            </div>
                        @endif
                    @endforeach
                    @if (!$historia->motivo_consulta && !$historia->antecedentes && !$historia->examen_fisico && !$historia->observaciones)
                        <p class="text-sm text-gray-400 italic text-center py-4">No se registraron detalles en esta atención.</p>
                    @endif

                    @if ($historia->diagnosticos->count() || $historia->enfermedades->count())
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Diagnósticos y enfermedades de esta consulta</p>
                            <div class="space-y-2">
                                @foreach ($historia->diagnosticos as $diag)
                                    <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 rounded-lg px-3 py-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                        <p class="text-sm text-indigo-900">{{ $diag->descripcion }} @if ($diag->tipo)<span class="text-[11px] font-semibold text-indigo-600">({{ $diag->tipo }})</span>@endif</p>
                                    </div>
                                @endforeach
                                @foreach ($historia->enfermedades as $enf)
                                    <div class="flex items-center gap-2 bg-rose-50 border border-rose-100 rounded-lg px-3 py-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                                        <p class="text-sm text-rose-900">{{ $enf->nombre }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <footer class="px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    <button type="button" data-cerrar-modal class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cerrar</button>
                </footer>
            </div>
        </div>
    @endforeach

    {{-- Diagnósticos --}}
    @foreach ($diagnosticos as $diagnostico)
        <div id="modal-detalle-diagnostico-{{ $diagnostico->id }}" class="modal-detalle fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-cerrar-modal></div>
            <div class="relative mx-auto mt-[8vh] w-[94%] max-w-xl bg-white rounded-2xl shadow-2xl max-h-[84vh] flex flex-col overflow-hidden">
                <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-600 to-blue-700 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-white/15 text-white flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-bold text-white truncate">Diagnóstico</h3>
                            <p class="text-xs text-indigo-100 truncate mt-0.5">{{ $diagnostico->fecha->format('d/m/Y') }} · Dr(a). {{ $diagnostico->medico->nombres ?? '' }} {{ $diagnostico->medico->apellidos ?? '' }}</p>
                        </div>
                    </div>
                    <button type="button" data-cerrar-modal class="text-white/80 hover:text-white p-1 flex-shrink-0" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </header>
                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider mb-1">Descripción</p>
                        <p class="text-sm text-gray-800">{{ $diagnostico->descripcion }}</p>
                    </div>
                    @if ($diagnostico->tipo)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tipo</span>
                            <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 rounded-full">{{ $diagnostico->tipo }}</span>
                        </div>
                    @endif
                    @if ($diagnostico->medico && $diagnostico->medico->especialidad)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Especialidad</span>
                            <span class="text-sm text-gray-700">{{ $diagnostico->medico->especialidad->nombre }}</span>
                        </div>
                    @endif
                    @if ($diagnostico->historiaClinica)
                        <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold text-teal-700 uppercase tracking-wider mb-1">Historia clínica asociada</p>
                            <p class="text-sm text-teal-900">{{ $diagnostico->historiaClinica->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($diagnostico->historiaClinica->motivo_consulta ?: 'Sin motivo', 70) }}</p>
                        </div>
                    @endif
                </div>
                <footer class="px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    <button type="button" data-cerrar-modal class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cerrar</button>
                </footer>
            </div>
        </div>
    @endforeach

    {{-- Enfermedades --}}
    @foreach ($enfermedades as $enfermedad)
        <div id="modal-detalle-enfermedad-{{ $enfermedad->id }}" class="modal-detalle fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-cerrar-modal></div>
            <div class="relative mx-auto mt-[8vh] w-[94%] max-w-xl bg-white rounded-2xl shadow-2xl max-h-[84vh] flex flex-col overflow-hidden">
                <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-600 to-red-700 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-white/15 text-white flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-bold text-white truncate">{{ $enfermedad->nombre }}</h3>
                            <p class="text-xs text-rose-100 truncate mt-0.5">{{ $enfermedad->fecha->format('d/m/Y') }} · Dr(a). {{ $enfermedad->medico->nombres ?? '' }} {{ $enfermedad->medico->apellidos ?? '' }}</p>
                        </div>
                    </div>
                    <button type="button" data-cerrar-modal class="text-white/80 hover:text-white p-1 flex-shrink-0" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </header>
                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    @if ($enfermedad->descripcion)
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold text-rose-700 uppercase tracking-wider mb-1">Descripción</p>
                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $enfermedad->descripcion }}</p>
                        </div>
                    @endif
                    @if ($enfermedad->medico && $enfermedad->medico->especialidad)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Especialidad</span>
                            <span class="text-sm text-gray-700">{{ $enfermedad->medico->especialidad->nombre }}</span>
                        </div>
                    @endif
                    @if ($enfermedad->historiaClinica)
                        <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold text-teal-700 uppercase tracking-wider mb-1">Historia clínica asociada</p>
                            <p class="text-sm text-teal-900">{{ $enfermedad->historiaClinica->fecha->format('d/m/Y') }} — {{ \Illuminate\Support\Str::limit($enfermedad->historiaClinica->motivo_consulta ?: 'Sin motivo', 70) }}</p>
                        </div>
                    @endif
                </div>
                <footer class="px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    <button type="button" data-cerrar-modal class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cerrar</button>
                </footer>
            </div>
        </div>
    @endforeach

    {{-- Procedimientos --}}
    @foreach ($procedimientos as $procedimiento)
        <div id="modal-detalle-procedimiento-{{ $procedimiento->id }}" class="modal-detalle fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-cerrar-modal></div>
            <div class="relative mx-auto mt-[8vh] w-[94%] max-w-xl bg-white rounded-2xl shadow-2xl max-h-[84vh] flex flex-col overflow-hidden">
                <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-500 to-orange-600 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-white/15 text-white flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-bold text-white truncate">{{ $procedimiento->descripcion }}</h3>
                            <p class="text-xs text-amber-100 truncate mt-0.5">{{ $procedimiento->fecha->format('d/m/Y') }} · Dr(a). {{ $procedimiento->medico->nombres ?? '' }} {{ $procedimiento->medico->apellidos ?? '' }}</p>
                        </div>
                    </div>
                    <button type="button" data-cerrar-modal class="text-white/80 hover:text-white p-1 flex-shrink-0" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </header>
                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    @if ($procedimiento->cita)
                        <span class="inline-flex items-center text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-full">Realizado en la cita #{{ $procedimiento->cita->id }}</span>
                    @endif
                    @if ($procedimiento->tipoProcedimiento)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tipo</span>
                            <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-0.5 rounded-full">{{ $procedimiento->tipoProcedimiento->nombre }}</span>
                        </div>
                    @endif
                    @if ($procedimiento->observaciones)
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-1">Observaciones</p>
                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $procedimiento->observaciones }}</p>
                        </div>
                    @endif
                    @if ($procedimiento->detalles->count())
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Detalles del procedimiento</p>
                            <div class="space-y-2">
                                @foreach ($procedimiento->detalles as $detalle)
                                    <div class="bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm text-gray-800">{{ $detalle->descripcion }}</p>
                                            @if ($detalle->cantidad)
                                                <span class="text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full">x{{ $detalle->cantidad }}</span>
                                            @endif
                                        </div>
                                        @if ($detalle->resultado)
                                            <p class="text-xs text-gray-500 mt-1">Resultado: {{ $detalle->resultado }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <footer class="px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    <button type="button" data-cerrar-modal class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Cerrar</button>
                </footer>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        (function () {
            var body = document.body;

            function cerrarTodos() {
                document.querySelectorAll('.modal-detalle:not(.hidden)').forEach(function (m) {
                    m.classList.add('hidden');
                });
                body.style.overflow = '';
            }

            document.addEventListener('click', function (e) {
                var abrir = e.target.closest('[data-ver-detalle]');
                if (abrir) {
                    var modal = document.getElementById('modal-detalle-' + abrir.getAttribute('data-ver-detalle'));
                    if (modal) {
                        modal.classList.remove('hidden');
                        body.style.overflow = 'hidden';
                    }
                    return;
                }

                if (e.target.closest('[data-cerrar-modal]')) {
                    cerrarTodos();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { cerrarTodos(); }
            });
        })();
    </script>
@endpush
