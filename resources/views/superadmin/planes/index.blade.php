@extends('superadmin.layout')
@section('titulo', 'Planes')

@section('contenido')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Planes</h1>
        <p class="text-sm text-gray-500 mt-1">Los planes que se venden en la página. Cambiar un precio o un texto aplica a las compras nuevas; las licencias ya compradas no cambian.</p>
    </div>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if ($planes->isEmpty())
            <div class="px-5 py-14 text-center">
                <p class="text-sm font-semibold text-gray-700">No hay planes registrados.</p>
                <p class="text-sm text-gray-500 mt-1">Se cargan con el seeder: <code class="bg-gray-100 px-1.5 py-0.5 rounded">php artisan db:seed --class=PlanSeeder</code></p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-5 py-3 text-center">Orden</th>
                            <th class="px-5 py-3">Plan</th>
                            <th class="px-5 py-3">Precio</th>
                            <th class="px-5 py-3">Periodo</th>
                            <th class="px-5 py-3 text-center">Empresas</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($planes as $plan)
                            <tr class="hover:bg-gray-50/60 {{ $plan->activo ? '' : 'opacity-70' }}">
                                <td class="px-5 py-3 text-center text-gray-500">{{ $plan->orden }}</td>
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-gray-900">
                                        {{ $plan->nombre }}
                                        @if ($plan->destacada)
                                            <span class="ml-1 align-middle rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5">Destacado</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $plan->etiqueta ?: '—' }} · {{ $plan->slug }}</p>
                                </td>
                                <td class="px-5 py-3 font-semibold text-gray-900">
                                    {{ $plan->precio_total ? '$' . number_format($plan->precio_total, 0, ',', '.') : 'Gratis' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $plan->periodo ?: '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('superadmin.empresas.index') }}"
                                       class="inline-block min-w-[2rem] rounded-full bg-gray-100 text-gray-700 text-xs font-bold px-2.5 py-1">{{ $empresas[$plan->id] ?? 0 }}</a>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($plan->activo)
                                        <span class="inline-block rounded-full bg-teal-50 text-teal-700 text-xs font-bold px-2.5 py-1">Activo</span>
                                    @else
                                        <span class="inline-block rounded-full bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('superadmin.planes.edit', $plan->id) }}"
                                           class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1.5 transition">Editar</a>
                                        <form method="POST" action="{{ route('superadmin.planes.activo', $plan->id) }}"
                                              onsubmit="return confirm('{{ $plan->activo ? '¿Desactivar este plan? Ya no se mostrará para comprar.' : '¿Activar este plan?' }}');">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="rounded-lg text-xs font-bold px-3 py-1.5 transition {{ $plan->activo ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-teal-50 text-teal-700 hover:bg-teal-100' }}">
                                                {{ $plan->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection