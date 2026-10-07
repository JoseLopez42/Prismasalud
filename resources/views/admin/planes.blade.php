@extends('admin.layout')

@section('titulo', 'Planes disponibles')
@section('encabezado', 'Planes disponibles')

@section('contenido')

    <p class="text-sm text-gray-500 mb-6">Elige el plan que quieres activar para tu IPS. El pago es simulado, tu cuenta ya está identificada.</p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach ($planes as $plan)
            <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col hover:border-teal-300 hover:shadow-md transition">
                <p class="text-[.68rem] font-extrabold uppercase tracking-wider text-teal-600 mb-1">{{ $plan['etiqueta'] }}</p>
                <h3 class="text-lg font-extrabold text-gray-900 mb-2">{{ $plan['nombre'] }}</h3>
                <p class="text-sm text-gray-500 mb-4 flex-1">{{ $plan['descripcion'] }}</p>

                <div class="mb-4">
                    @if ($plan['precio_total'] === 0)
                        <p class="text-xl font-extrabold text-teal-600">Gratis</p>
                    @else
                        <p class="text-xl font-extrabold text-gray-900">${{ number_format($plan['precio_total'], 0, ',', '.') }}</p>
                    @endif
                    <p class="text-xs text-gray-400">{{ $plan['periodo'] }}</p>
                </div>

                <ul class="space-y-1.5 mb-5">
                    @foreach (array_slice($plan['beneficios'], 0, 3) as $beneficio)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg>
                            <span>{{ $beneficio }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('comprar.form', ['plan' => $plan['id']]) }}"
                   class="mt-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-extrabold text-white rounded-lg"
                   style="background: linear-gradient(120deg, #0C7B93, #12A594);">
                    Elegir este plan
                </a>
            </div>
        @endforeach
    </div>

@endsection