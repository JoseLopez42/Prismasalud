@extends('superadmin.layout')
@section('titulo', 'Pagos')

@php
    $hayFiltros = $f['q'] !== '' || $f['estado'] !== '' || $f['tipo'] !== '' || $f['plan'] !== '' || $f['desde'] !== '' || $f['hasta'] !== '';
    $campo = 'rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
@endphp

@section('contenido')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Pagos</h1>
        <p class="text-sm text-gray-500 mt-1">Las compras y renovaciones de licencias. Aquí solo se consulta.</p>
    </div>

    <section class="grid sm:grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-bold text-gray-500 uppercase">{{ $hayFiltros ? 'Pagos con este filtro' : 'Pagos registrados' }}</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($n, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-bold text-gray-500 uppercase">Total aprobado {{ $hayFiltros ? '(con este filtro)' : '' }}</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">${{ number_format($aprobado, 0, ',', '.') }} <span class="text-sm font-semibold text-gray-400">COP</span></p>
        </div>
    </section>

    <form method="GET" action="{{ route('superadmin.pagos.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="q" class="block text-[11px] font-bold text-gray-500 mb-1">Buscar</label>
            <input id="q" type="search" name="q" value="{{ $f['q'] }}" placeholder="Empresa, NIT o referencia…" class="{{ $campo }} w-full">
        </div>
        <div>
            <label for="estado" class="block text-[11px] font-bold text-gray-500 mb-1">Estado</label>
            <select id="estado" name="estado" class="{{ $campo }}">
                <option value="">Todos</option>
                @foreach ($estados as $e)
                    <option value="{{ $e }}" @selected($f['estado'] === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="tipo" class="block text-[11px] font-bold text-gray-500 mb-1">Tipo</label>
            <select id="tipo" name="tipo" class="{{ $campo }}">
                <option value="">Todos</option>
                @foreach ($tipos as $t)
                    <option value="{{ $t }}" @selected($f['tipo'] === $t)>{{ $t === 'renovacion' ? 'Renovación' : ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="plan" class="block text-[11px] font-bold text-gray-500 mb-1">Plan</label>
            <select id="plan" name="plan" class="{{ $campo }}">
                <option value="">Todos</option>
                @foreach ($planes as $p)
                    <option value="{{ $p->id }}" @selected($f['plan'] === (string) $p->id)>{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="desde" class="block text-[11px] font-bold text-gray-500 mb-1">Desde</label>
            <input id="desde" type="date" name="desde" value="{{ $f['desde'] }}" class="{{ $campo }}">
        </div>
        <div>
            <label for="hasta" class="block text-[11px] font-bold text-gray-500 mb-1">Hasta</label>
            <input id="hasta" type="date" name="hasta" value="{{ $f['hasta'] }}" class="{{ $campo }}">
        </div>
        <button type="submit" class="rounded-lg bg-prisma-azul hover:bg-prisma-turquesa text-white text-sm font-bold px-4 py-2 transition">Filtrar</button>
        @if ($hayFiltros)
            <a href="{{ route('superadmin.pagos.index') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600 text-sm font-bold px-4 py-2 transition">Limpiar</a>
        @endif
    </form>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if ($pagos->isEmpty())
            <div class="px-5 py-14 text-center">
                <p class="text-sm font-semibold text-gray-700">No hay pagos para mostrar.</p>
                <p class="text-sm text-gray-500 mt-1">{{ $hayFiltros ? 'Prueba con otros filtros o límpialos.' : 'Cuando una empresa compre un plan, el pago aparecerá aquí.' }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-5 py-3">Fecha</th>
                            <th class="px-5 py-3">Empresa</th>
                            <th class="px-5 py-3">Plan</th>
                            <th class="px-5 py-3">Tipo</th>
                            <th class="px-5 py-3">Referencia</th>
                            <th class="px-5 py-3 text-right">Monto</th>
                            <th class="px-5 py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($pagos as $pago)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 text-gray-600 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($pago->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3">
                                    @if ($pago->licencia_id)
                                        <a href="{{ route('superadmin.empresas.edit', $pago->licencia_id) }}" class="font-semibold text-gray-900 hover:text-teal-700 hover:underline">{{ $pago->nombre_ips }}</a>
                                        <p class="text-xs text-gray-500">NIT {{ $pago->nit }}</p>
                                    @else
                                        <span class="text-gray-400">Empresa eliminada</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $pago->plan_nombre ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $pago->tipo === 'renovacion' ? 'Renovación' : ucfirst($pago->tipo) }}</td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $pago->referencia_pago ?: '—' }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">${{ number_format($pago->monto, 0, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    @if ($pago->estado === 'aprobado')
                                        <span class="inline-block rounded-full bg-teal-50 text-teal-700 text-xs font-bold px-2.5 py-1">Aprobado</span>
                                    @elseif ($pago->estado === 'pendiente')
                                        <span class="inline-block rounded-full bg-amber-50 text-amber-600 text-xs font-bold px-2.5 py-1">Pendiente</span>
                                    @else
                                        <span class="inline-block rounded-full bg-red-50 text-red-600 text-xs font-bold px-2.5 py-1">{{ ucfirst($pago->estado) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($pagos->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $pagos->links() }}</div>
            @endif
        @endif
    </section>
@endsection