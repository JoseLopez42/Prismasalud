@extends('medico.layout')

@section('titulo', 'Mis horarios')
@section('encabezado', 'Mis horarios')

@section('contenido')

    {{-- Encabezado --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 mb-6">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center flex-shrink-0">
                @include('admin.partials.icono', ['icono' => 'horarios', 'clase' => 'w-7 h-7'])
            </div>
            <div class="flex-1">
                <h2 class="text-xl md:text-2xl font-bold">Mi agenda programada</h2>
                <p class="text-white/80 text-sm mt-1">
                    {{ $proximos->count() }} {{ $proximos->count() === 1 ? 'horario próximo' : 'horarios próximos' }}
                    · Dr(a). {{ $medico->nombres ?? '' }} {{ $medico->apellidos ?? '' }}
                </p>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
    </div>

    {{-- Horarios próximos (hoy en adelante) --}}
    <div class="mb-8">
        <h3 class="text-base font-bold text-gray-900 mb-1">Horarios programados</h3>
        <p class="text-sm text-gray-500 mb-4">Haz clic en "Ver pacientes" para abrir el detalle del horario</p>

        @if ($proximos->count())
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($proximos as $horario)
                    @php $esHoy = $horario->fecha->isToday(); @endphp
                    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden flex {{ $esHoy ? 'border-teal-300 ring-1 ring-teal-100' : 'border-gray-200' }}">
                        <div class="w-1.5 {{ $esHoy ? 'bg-teal-500' : 'bg-gray-200' }} flex-shrink-0"></div>
                        <div class="flex-1 p-5">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-xl bg-teal-50 border border-teal-100 flex flex-col items-center justify-center flex-shrink-0">
                                        <span class="text-base font-extrabold text-teal-700 leading-none">{{ $horario->fecha->format('d') }}</span>
                                        <span class="text-[10px] font-semibold text-teal-600 uppercase">{{ $horario->fecha->locale('es')->translatedFormat('M') }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 capitalize">{{ $horario->dia_semana }}</p>
                                        @if ($esHoy)
                                            <span class="text-[11px] font-bold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-full">Hoy</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border flex-shrink-0 {{ $horario->total_pacientes > 0 ? 'text-blue-700 bg-blue-50 border-blue-100' : 'text-gray-500 bg-gray-50 border-gray-200' }}">
                                    {{ $horario->total_pacientes }} {{ $horario->total_pacientes === 1 ? 'paciente' : 'pacientes' }}
                                </span>
                            </div>

                            <dl class="text-sm space-y-1.5 mb-4">
                                <div class="flex items-center gap-2 text-gray-700">
                                    @include('admin.partials.icono', ['icono' => 'citas', 'clase' => 'w-4 h-4 text-gray-400'])
                                    <span>{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    @include('admin.partials.icono', ['icono' => 'ubicaciones', 'clase' => 'w-4 h-4 text-gray-400'])
                                    <span>{{ $horario->consultorio->nombre ?? 'Sin consultorio' }}</span>
                                </div>
                            </dl>

                            <div class="pt-3 border-t border-gray-100">
                                <a href="{{ route('medico.horario.detalle', $horario->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white border border-green-600 text-xs font-semibold hover:bg-green-700 transition">
                                    @include('admin.partials.icono', ['icono' => 'lista', 'clase' => 'w-4 h-4'])
                                    Ver pacientes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                <div class="inline-flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-teal-50 flex items-center justify-center mb-4">
                        @include('admin.partials.icono', ['icono' => 'horarios', 'clase' => 'w-10 h-10 text-teal-400'])
                    </div>
                    <p class="text-base font-semibold text-gray-700">No tienes horarios programados</p>
                    <p class="text-sm text-gray-400 mt-1">Cuando el administrador programe tu agenda, aparecerá aquí.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Horarios anteriores --}}
    @if ($anteriores->count())
        <h3 class="text-base font-bold text-gray-900 mb-1">Horarios anteriores</h3>
        <p class="text-sm text-gray-500 mb-4">Últimos 10 horarios ya pasados</p>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm divide-y divide-gray-100">
            @foreach ($anteriores as $horario)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-gray-600 leading-none">{{ $horario->fecha->format('d') }}</span>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $horario->fecha->locale('es')->translatedFormat('M') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate capitalize">{{ $horario->dia_semana }} · {{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $horario->consultorio->nombre ?? 'Sin consultorio' }}
                            · {{ $horario->total_pacientes }} {{ $horario->total_pacientes === 1 ? 'paciente' : 'pacientes' }}
                        </p>
                    </div>
                    <a href="{{ route('medico.horario.detalle', $horario->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 border border-teal-100 text-xs font-semibold hover:bg-teal-600 hover:text-white transition flex-shrink-0">
                        Ver
                    </a>
                </div>
            @endforeach
        </div>
    @endif

@endsection
