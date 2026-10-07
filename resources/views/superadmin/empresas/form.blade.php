@extends('superadmin.layout')
@php $editando = $licencia !== null; @endphp
@section('titulo', $editando ? 'Editar empresa' : 'Nueva empresa')

@php
    $input = 'w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $borde = fn ($campo) => $errors->has($campo) ? 'border-red-500' : 'border-gray-300';
    $valor = fn ($campo, $defecto = '') => old($campo, $editando ? ($licencia->{$campo} ?? $defecto) : $defecto);
    $fecha = fn ($campo, $defecto = '') => old($campo, $editando && $licencia->{$campo} ? $licencia->{$campo}->format('Y-m-d') : $defecto);
@endphp

@section('contenido')
    <a href="{{ route('superadmin.empresas.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-900">← Volver a Empresas</a>
    <h1 class="text-2xl font-extrabold text-gray-900 mt-2">{{ $editando ? 'Editar empresa' : 'Nueva empresa' }}</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">
        {{ $editando ? 'Corrige lo que se haya escrito mal. Los cambios se ven al instante.' : 'Registra una IPS o EPS que compró una licencia.' }}
    </p>

    <form method="POST" novalidate data-validar
          action="{{ $editando ? route('superadmin.empresas.update', $licencia->id) : route('superadmin.empresas.store') }}"
          class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-8">
        @csrf
        @if ($editando) @method('PUT') @endif

        {{-- Datos de la empresa --}}
        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Datos de la empresa</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="nombre_ips" class="block text-xs font-bold text-gray-700 mb-1">Nombre de la empresa *</label>
                    <input id="nombre_ips" name="nombre_ips" type="text" value="{{ $valor('nombre_ips') }}" autofocus
                           data-v data-requerido="Escribe el nombre de la empresa." data-min="3" data-max="150"
                           class="{{ $input }} {{ $borde('nombre_ips') }}">
                    <p data-error-for="nombre_ips" class="text-xs text-red-600 mt-1 {{ $errors->has('nombre_ips') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nombre_ips') }}</p>
                </div>

                <div>
                    <label for="nit" class="block text-xs font-bold text-gray-700 mb-1">NIT *</label>
                    <input id="nit" name="nit" type="text" value="{{ $valor('nit') }}" placeholder="900123456-7"
                           data-v data-requerido="Escribe el NIT."
                           data-patron="^[0-9]{6,12}(-[0-9])?$" data-patron-msg="Solo números (6 a 12) y, si quieres, el dígito de verificación: 900123456-7."
                           class="{{ $input }} {{ $borde('nit') }}">
                    <p data-error-for="nit" class="text-xs text-red-600 mt-1 {{ $errors->has('nit') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nit') }}</p>
                </div>

                <div>
                    <label for="estado" class="block text-xs font-bold text-gray-700 mb-1">Estado *</label>
                    <select id="estado" name="estado" data-v data-requerido="Elige el estado." class="{{ $input }} {{ $borde('estado') }} bg-white">
                        @foreach (['activa' => 'Activa', 'prueba' => 'En prueba', 'suspendida' => 'Suspendida'] as $clave => $texto)
                            <option value="{{ $clave }}" @selected($valor('estado', 'activa') === $clave)>{{ $texto }}</option>
                        @endforeach
                    </select>
                    <p data-error-for="estado" class="text-xs text-red-600 mt-1 {{ $errors->has('estado') ? '' : 'hidden' }}" role="alert">{{ $errors->first('estado') }}</p>
                </div>
            </div>
        </section>

        {{-- Contacto --}}
        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Contacto</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="contacto_nombre" class="block text-xs font-bold text-gray-700 mb-1">Nombre de la persona de contacto</label>
                    <input id="contacto_nombre" name="contacto_nombre" type="text" value="{{ $valor('contacto_nombre') }}"
                           data-v data-max="150" class="{{ $input }} {{ $borde('contacto_nombre') }}">
                    <p data-error-for="contacto_nombre" class="text-xs text-red-600 mt-1 {{ $errors->has('contacto_nombre') ? '' : 'hidden' }}" role="alert">{{ $errors->first('contacto_nombre') }}</p>
                </div>
                <div>
                    <label for="contacto_email" class="block text-xs font-bold text-gray-700 mb-1">Correo de contacto</label>
                    <input id="contacto_email" name="contacto_email" type="email" value="{{ $valor('contacto_email') }}"
                           data-v data-email data-max="150" class="{{ $input }} {{ $borde('contacto_email') }}">
                    <p data-error-for="contacto_email" class="text-xs text-red-600 mt-1 {{ $errors->has('contacto_email') ? '' : 'hidden' }}" role="alert">{{ $errors->first('contacto_email') }}</p>
                </div>
                <div>
                    <label for="contacto_telefono" class="block text-xs font-bold text-gray-700 mb-1">Teléfono de contacto</label>
                    <input id="contacto_telefono" name="contacto_telefono" type="text" inputmode="tel" value="{{ $valor('contacto_telefono') }}"
                           data-v data-patron="^[0-9+\s-]{7,20}$" data-patron-msg="Solo números (7 a 20); puede tener + o espacios."
                           class="{{ $input }} {{ $borde('contacto_telefono') }}">
                    <p data-error-for="contacto_telefono" class="text-xs text-red-600 mt-1 {{ $errors->has('contacto_telefono') ? '' : 'hidden' }}" role="alert">{{ $errors->first('contacto_telefono') }}</p>
                </div>
            </div>
        </section>

        {{-- Licencia --}}
        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Licencia</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label for="plan_id" class="block text-xs font-bold text-gray-700 mb-1">Plan *</label>
                    <select id="plan_id" name="plan_id" data-v data-requerido="Elige un plan." class="{{ $input }} {{ $borde('plan_id') }} bg-white">
                        <option value="">Elige un plan…</option>
                        @foreach ($planes as $plan)
                            <option value="{{ $plan->id }}" @selected((string) $valor('plan_id') === (string) $plan->id)>{{ $plan->nombre }}</option>
                        @endforeach
                    </select>
                    <p data-error-for="plan_id" class="text-xs text-red-600 mt-1 {{ $errors->has('plan_id') ? '' : 'hidden' }}" role="alert">{{ $errors->first('plan_id') }}</p>
                </div>
                <div>
                    <label for="fecha_inicio" class="block text-xs font-bold text-gray-700 mb-1">Fecha de inicio *</label>
                    <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ $fecha('fecha_inicio', now()->format('Y-m-d')) }}"
                           data-v data-requerido="Elige la fecha de inicio." data-revalida="#fecha_fin"
                           class="{{ $input }} {{ $borde('fecha_inicio') }}">
                    <p data-error-for="fecha_inicio" class="text-xs text-red-600 mt-1 {{ $errors->has('fecha_inicio') ? '' : 'hidden' }}" role="alert">{{ $errors->first('fecha_inicio') }}</p>
                </div>
                <div>
                    <label for="fecha_fin" class="block text-xs font-bold text-gray-700 mb-1">Vence el *</label>
                    <input id="fecha_fin" name="fecha_fin" type="date" value="{{ $fecha('fecha_fin') }}"
                           data-v data-requerido="Elige la fecha de vencimiento." data-fecha-min="#fecha_inicio" data-fecha-msg="El vencimiento no puede ser antes del inicio."
                           class="{{ $input }} {{ $borde('fecha_fin') }}">
                    <p data-error-for="fecha_fin" class="text-xs text-red-600 mt-1 {{ $errors->has('fecha_fin') ? '' : 'hidden' }}" role="alert">{{ $errors->first('fecha_fin') }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-[11px] text-gray-400 self-center">Desde el inicio:</span>
                        <button type="button" data-meses="1"  class="rounded-md border border-gray-300 hover:bg-gray-100 text-[11px] font-bold text-gray-600 px-2 py-1">+1 mes</button>
                        <button type="button" data-meses="12" class="rounded-md border border-gray-300 hover:bg-gray-100 text-[11px] font-bold text-gray-600 px-2 py-1">+1 año</button>
                        <button type="button" data-meses="24" class="rounded-md border border-gray-300 hover:bg-gray-100 text-[11px] font-bold text-gray-600 px-2 py-1">+2 años</button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Notas --}}
        <section>
            <label for="notas" class="block text-xs font-bold text-gray-700 mb-1">Notas internas (opcional)</label>
            <textarea id="notas" name="notas" rows="3" data-v data-max="1000"
                      class="{{ $input }} {{ $borde('notas') }}">{{ $valor('notas') }}</textarea>
            <p data-error-for="notas" class="text-xs text-red-600 mt-1 {{ $errors->has('notas') ? '' : 'hidden' }}" role="alert">{{ $errors->first('notas') }}</p>
        </section>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 transition disabled:opacity-60">
                {{ $editando ? 'Guardar cambios' : 'Crear empresa' }}
            </button>
            <a href="{{ route('superadmin.empresas.index') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold px-5 py-2.5 transition">Cancelar</a>
        </div>
    </form>

    @include('superadmin._validador')

@push('scripts')
<script>
    // Botones "+1 mes / +1 año / +2 años": calculan el vencimiento a partir de la fecha de inicio
    (function () {
        var inicio = document.getElementById('fecha_inicio');
        var fin = document.getElementById('fecha_fin');
        document.querySelectorAll('[data-meses]').forEach(function (b) {
            b.addEventListener('click', function () {
                if (!inicio.value) { inicio.focus(); return; }
                var p = inicio.value.split('-');
                var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                d.setMonth(d.getMonth() + parseInt(b.getAttribute('data-meses'), 10));
                var mm = ('0' + (d.getMonth() + 1)).slice(-2), dd = ('0' + d.getDate()).slice(-2);
                fin.value = d.getFullYear() + '-' + mm + '-' + dd;
                fin.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    })();
</script>
@endpush
@endsection