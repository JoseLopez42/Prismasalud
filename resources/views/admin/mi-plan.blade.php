@extends('admin.layout')

@section('titulo', 'Mi Plan')
@section('encabezado', 'Mi Plan')

@section('contenido')

    @if (session('exito'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold text-emerald-800">{{ session('exito') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if (!$licenciaActual)
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
            <p class="text-gray-500 mb-4">Tu cuenta no tiene un plan de PrismaSalud asociado todavía.</p>
            <a href="{{ route('admin.planes') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-extrabold text-white rounded-lg"
               style="background: linear-gradient(120deg, #0C7B93, #12A594);">
                Ver planes y comprar
            </a>
        </div>
    @else
        @php
            $diasRestantes = $licenciaActual->diasRestantes();
            $vencida = $licenciaActual->estaVencida();
            $porVencer = $licenciaActual->estaPorVencer();

            // Barra de progreso de la vigencia
            $totalDias = $licenciaActual->fecha_inicio && $licenciaActual->fecha_fin
                ? $licenciaActual->fecha_inicio->diffInDays($licenciaActual->fecha_fin)
                : 0;
             $diasTranscurridos = $licenciaActual->fecha_inicio
                ? (int) floor($licenciaActual->fecha_inicio->diffInDays(now()->min($licenciaActual->fecha_fin ?? now())))
                : 0;
            $porcentajeUsado = $totalDias > 0 ? min(100, round(($diasTranscurridos / $totalDias) * 100)) : 0;

            // Planes superiores al actual, para sugerir upgrade (ahora desde la tabla planes)
            $todosLosPlanes = \App\Models\Plan::catalogoArray();
            $indiceActual = collect($todosLosPlanes)->search(fn ($p) => $p['id'] === $licenciaActual->plan);
            $planesSuperiores = $indiceActual !== false
                ? array_slice($todosLosPlanes, $indiceActual + 1, 2)
                : [];
        @endphp

        <div class="grid lg:grid-cols-[1.4fr_1fr] gap-6 items-start">

            <div class="space-y-6">

                {{-- Tarjeta principal del plan --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-xl font-extrabold text-gray-900">{{ $planInfo['nombre'] ?? $licenciaActual->nombrePlan() }}</h2>
                        <span class="text-xs font-bold px-3 py-1 rounded-full
                            {{ $licenciaActual->estado === 'activa' ? 'bg-emerald-50 text-emerald-700' : '' }}
                            {{ $licenciaActual->estado === 'prueba' ? 'bg-sky-50 text-sky-700' : '' }}
                            {{ in_array($licenciaActual->estado, ['vencida','suspendida','cancelada']) ? 'bg-red-50 text-red-700' : '' }}">
                            {{ ucfirst($licenciaActual->estado) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">{{ $planInfo['descripcion'] ?? '' }}</p>

                    <div class="grid sm:grid-cols-3 gap-4 mb-5">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Inicio</p>
                            <p class="font-bold text-gray-900">{{ $licenciaActual->fecha_inicio?->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Vence</p>
                            <p class="font-bold text-gray-900">{{ $licenciaActual->fecha_fin?->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-400 mb-1">Días restantes</p>
                            <p class="font-bold {{ $vencida ? 'text-red-600' : ($porVencer ? 'text-amber-600' : 'text-gray-900') }}">
                                {{ $vencida ? 'Vencida' : $diasRestantes . ' días' }}
                            </p>
                        </div>
                    </div>

                    {{-- Barra de progreso de la vigencia --}}
                    @if ($totalDias > 0)
                        <div class="mb-5">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                                <span>Vigencia usada</span>
                                <span>{{ $diasTranscurridos }} de {{ $totalDias }} días ({{ $porcentajeUsado }}%)</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $vencida ? 'bg-red-500' : ($porVencer ? 'bg-amber-500' : 'bg-teal-500') }}"
                                     style="width: {{ $porcentajeUsado }}%;"></div>
                            </div>
                        </div>
                    @endif

                    @if ($vencida)
                        <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700 mb-5">
                            Tu licencia está vencida. Renueva tu plan para seguir usando el sistema.
                        </div>
                    @elseif ($porVencer)
                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700 mb-5">
                            Tu licencia vence pronto. Renueva a tiempo para no perder el acceso al sistema.
                        </div>
                    @endif

                    {{-- Botón renovar --}}
                    <a href="{{ route('admin.planes') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-extrabold text-white rounded-lg"
                       style="background: linear-gradient(120deg, #0C7B93, #12A594);">
                        Renovar mi plan
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>

                {{-- Historial de pagos (transacciones reales) --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <p class="text-sm font-extrabold text-gray-700 mb-4">Historial de pagos</p>

                    @if ($historialPagos->isEmpty())
                        <p class="text-sm text-gray-400">Todavía no hay pagos registrados para esta licencia.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($historialPagos as $pago)
                                <div class="flex items-center justify-between text-sm border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $pago->tipo === 'renovacion' ? 'Renovación' : 'Compra' }}
                                            <span class="text-gray-400 font-normal">· {{ $pago->referencia_pago }}</span>
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $pago->created_at?->format('d/m/Y g:i A') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">
                                            {{ $pago->monto > 0 ? '$' . number_format($pago->monto, 0, ',', '.') : 'Gratis' }}
                                        </p>
                                        <span class="text-[.65rem] font-bold uppercase tracking-wider text-emerald-600">{{ $pago->estado }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Planes superiores sugeridos --}}
                @if (count($planesSuperiores) > 0)
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <p class="text-sm font-extrabold text-gray-700 mb-1">¿Quieres más capacidad?</p>
                        <p class="text-xs text-gray-500 mb-4">Estos planes te dan más usuarios, sedes y funciones.</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach ($planesSuperiores as $sugerido)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <p class="font-bold text-gray-900 text-sm">{{ $sugerido['nombre'] }}</p>
                                    <p class="text-xs text-gray-500 mb-3">{{ $sugerido['periodo'] }}</p>
                                    <a href="{{ route('comprar.form', ['plan' => $sugerido['id']]) }}"
                                       class="inline-flex items-center gap-1 text-xs font-extrabold text-teal-700">
                                        Ver este plan
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">

                {{-- Beneficios --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <p class="text-sm font-extrabold text-gray-700 mb-3">Beneficios incluidos</p>
                    <ul class="space-y-2">
                        @foreach (($planInfo['beneficios'] ?? []) as $beneficio)
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg>
                                <span>{{ $beneficio }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Datos de contacto / factura --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <p class="text-sm font-extrabold text-gray-700 mb-3">Datos de tu cuenta</p>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">IPS / Clínica</p>
                            <p class="font-semibold text-gray-800">{{ $licenciaActual->nombre_ips }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">NIT</p>
                            <p class="font-semibold text-gray-800">{{ $licenciaActual->nit }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Contacto</p>
                            <p class="font-semibold text-gray-800">{{ $licenciaActual->contacto_nombre }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Correo</p>
                            <p class="font-semibold text-gray-800">{{ $licenciaActual->contacto_email }}</p>
                        </div>
                        @if ($licenciaActual->contacto_telefono)
                            <div>
                                <p class="text-xs text-gray-400">Teléfono</p>
                                <p class="font-semibold text-gray-800">{{ $licenciaActual->contacto_telefono }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection