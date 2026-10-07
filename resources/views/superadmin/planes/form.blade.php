@extends('superadmin.layout')
@section('titulo', 'Editar plan')

@php
    $input = 'w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $borde = fn ($campo) => $errors->has($campo) ? 'border-red-500' : 'border-gray-300';
    $valor = fn ($campo, $defecto = '') => old($campo, $plan->{$campo} ?? $defecto);
    $textoBeneficios = old('beneficios_texto', implode("\n", $plan->beneficios ?? []));
    $textoNoIncluye = old('no_incluye_texto', implode("\n", $plan->no_incluye ?? []));
    $activo = old('nombre') !== null ? (bool) old('activo') : $plan->activo;
    $destacada = old('nombre') !== null ? (bool) old('destacada') : $plan->destacada;
@endphp

@section('contenido')
    <a href="{{ route('superadmin.planes.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-900">← Volver a Planes</a>
    <h1 class="text-2xl font-extrabold text-gray-900 mt-2">Editar plan</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Los cambios se ven en la página de planes y aplican a las compras nuevas. Las licencias ya compradas no se modifican.</p>

    <form method="POST" action="{{ route('superadmin.planes.update', $plan->id) }}" novalidate data-validar
          class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-8">
        @csrf @method('PUT')

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Datos del plan</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Identificador</label>
                    <input type="text" value="{{ $plan->slug }}" disabled class="{{ $input }} border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed">
                    <p class="text-[11px] text-gray-400 mt-1">No se puede cambiar: es parte del enlace de compra.</p>
                </div>

                <div>
                    <label for="nombre" class="block text-xs font-bold text-gray-700 mb-1">Nombre *</label>
                    <input id="nombre" name="nombre" type="text" value="{{ $valor('nombre') }}" autofocus
                           data-v data-requerido="Escribe el nombre del plan." data-max="60"
                           class="{{ $input }} {{ $borde('nombre') }}">
                    <p data-error-for="nombre" class="text-xs text-red-600 mt-1 {{ $errors->has('nombre') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nombre') }}</p>
                </div>

                <div>
                    <label for="etiqueta" class="block text-xs font-bold text-gray-700 mb-1">Etiqueta</label>
                    <input id="etiqueta" name="etiqueta" type="text" value="{{ $valor('etiqueta') }}" data-v data-max="60"
                           class="{{ $input }} {{ $borde('etiqueta') }}">
                    <p data-error-for="etiqueta" class="text-xs text-red-600 mt-1 {{ $errors->has('etiqueta') ? '' : 'hidden' }}" role="alert">{{ $errors->first('etiqueta') }}</p>
                </div>

                <div>
                    <label for="periodo" class="block text-xs font-bold text-gray-700 mb-1">Periodo</label>
                    <input id="periodo" name="periodo" type="text" value="{{ $valor('periodo') }}" placeholder="por 12 meses" data-v data-max="40"
                           class="{{ $input }} {{ $borde('periodo') }}">
                    <p data-error-for="periodo" class="text-xs text-red-600 mt-1 {{ $errors->has('periodo') ? '' : 'hidden' }}" role="alert">{{ $errors->first('periodo') }}</p>
                </div>

                <div>
                    <label for="precio_total" class="block text-xs font-bold text-gray-700 mb-1">Precio total (COP) *</label>
                    <input id="precio_total" name="precio_total" type="text" inputmode="numeric" maxlength="12" value="{{ $valor('precio_total', 0) }}"
                           data-v data-requerido="Escribe el precio (si es gratis, pon 0)." data-patron="^[0-9.]{1,12}$" data-patron-msg="El precio solo lleva números, sin signos ni letras."
                           class="{{ $input }} {{ $borde('precio_total') }}">
                    <p class="text-[11px] text-gray-400 mt-1">Solo números. Para un plan gratis escribe 0.</p>
                    <p data-error-for="precio_total" class="text-xs text-red-600 mt-1 {{ $errors->has('precio_total') ? '' : 'hidden' }}" role="alert">{{ $errors->first('precio_total') }}</p>
                </div>

                <div>
                    <label for="orden" class="block text-xs font-bold text-gray-700 mb-1">Orden en la página *</label>
                    <input id="orden" name="orden" type="text" inputmode="numeric" maxlength="2" value="{{ $valor('orden', 0) }}"
                           data-v data-requerido="Escribe el orden (0 a 99)." data-patron="^[0-9]{1,2}$" data-patron-msg="El orden es un número de 0 a 99."
                           class="{{ $input }} {{ $borde('orden') }}">
                    <p data-error-for="orden" class="text-xs text-red-600 mt-1 {{ $errors->has('orden') ? '' : 'hidden' }}" role="alert">{{ $errors->first('orden') }}</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="descripcion" class="block text-xs font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="2" data-v data-max="500"
                              class="{{ $input }} {{ $borde('descripcion') }}">{{ $valor('descripcion') }}</textarea>
                    <p data-error-for="descripcion" class="text-xs text-red-600 mt-1 {{ $errors->has('descripcion') ? '' : 'hidden' }}" role="alert">{{ $errors->first('descripcion') }}</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="ideal" class="block text-xs font-bold text-gray-700 mb-1">Ideal para</label>
                    <textarea id="ideal" name="ideal" rows="2" data-v data-max="500"
                              class="{{ $input }} {{ $borde('ideal') }}">{{ $valor('ideal') }}</textarea>
                    <p data-error-for="ideal" class="text-xs text-red-600 mt-1 {{ $errors->has('ideal') ? '' : 'hidden' }}" role="alert">{{ $errors->first('ideal') }}</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Lo que incluye</h2>
            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <label for="beneficios_texto" class="block text-xs font-bold text-gray-700 mb-1">Incluye (una línea por ítem)</label>
                    <textarea id="beneficios_texto" name="beneficios_texto" rows="9"
                              class="{{ $input }} {{ $borde('beneficios_texto') }}">{{ $textoBeneficios }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Máximo 20 líneas de 150 caracteres.</p>
                    <p data-error-for="beneficios_texto" class="text-xs text-red-600 mt-1 {{ $errors->has('beneficios_texto') ? '' : 'hidden' }}" role="alert">{{ $errors->first('beneficios_texto') }}</p>
                </div>
                <div>
                    <label for="no_incluye_texto" class="block text-xs font-bold text-gray-700 mb-1">No incluye (una línea por ítem)</label>
                    <textarea id="no_incluye_texto" name="no_incluye_texto" rows="9"
                              class="{{ $input }} {{ $borde('no_incluye_texto') }}">{{ $textoNoIncluye }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Máximo 20 líneas de 150 caracteres.</p>
                    <p data-error-for="no_incluye_texto" class="text-xs text-red-600 mt-1 {{ $errors->has('no_incluye_texto') ? '' : 'hidden' }}" role="alert">{{ $errors->first('no_incluye_texto') }}</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Visibilidad</h2>
            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="activo" value="1" @checked($activo) class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    Plan activo (se muestra para comprar)
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="destacada" value="1" @checked($destacada) class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    Destacarlo en la página
                </label>
            </div>
            @if ($errors->has('activo'))
                <p class="text-xs text-red-600 mt-2" role="alert">{{ $errors->first('activo') }}</p>
            @endif
        </section>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 transition disabled:opacity-60">Guardar cambios</button>
            <a href="{{ route('superadmin.planes.index') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold px-5 py-2.5 transition">Cancelar</a>
        </div>
    </form>

    @include('superadmin._validador')
@endsection