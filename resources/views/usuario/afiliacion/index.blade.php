@extends('usuario.layout')

@section('titulo', 'Mi afiliación')
@section('encabezado', 'Mi afiliación')

@section('contenido')

    <div class="mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Mi afiliación</h2>
        <p class="text-sm text-gray-500">Datos de tu afiliación en PrismaSalud IPS</p>
    </div>

    {{-- Estado de afiliación --}}
    <div class="rounded-2xl p-5 md:p-6 mb-6 text-white {{ $eps ? 'bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57]' : 'bg-gradient-to-br from-gray-500 to-gray-600' }}">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center flex-shrink-0">
                @include('admin.partials.icono', ['icono' => 'eps', 'clase' => 'w-6 h-6'])
            </div>
            <div>
                <p class="text-white/70 text-xs font-semibold uppercase tracking-wide">Estado de afiliación</p>
                <p class="text-lg font-bold">{{ $eps ? 'Afiliación activa' : 'Afiliación pendiente' }}</p>
                <p class="text-white/80 text-sm">{{ $eps->nombre_eps ?? 'Aún no tienes una EPS asociada' }}</p>
            </div>
            @if ($eps)
                <span class="ml-auto hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-[#1A5C3A] bg-white px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activa
                </span>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 max-w-4xl">
        {{-- EPS --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                <span class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    @include('admin.partials.icono', ['icono' => 'eps', 'clase' => 'w-4 h-4'])
                </span>
                <h3 class="font-bold text-gray-900">EPS</h3>
            </div>

            @if ($eps)
                <dl class="text-sm space-y-2.5">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Nombre</dt><dd class="font-semibold text-gray-900 text-right">{{ $eps->nombre_eps }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">NIT</dt><dd class="text-gray-700 text-right">{{ $eps->nit_eps }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Teléfono</dt><dd class="text-gray-700 text-right">{{ $eps->telefono_contacto }}</dd></div>
                </dl>
            @else
                <p class="text-sm text-gray-400">Aún no tienes una EPS asociada. Contacta a la IPS.</p>
            @endif
        </div>

        {{-- Afiliado --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                <span class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    @include('admin.partials.icono', ['icono' => 'perfil', 'clase' => 'w-4 h-4'])
                </span>
                <h3 class="font-bold text-gray-900">Afiliado</h3>
            </div>
            <dl class="text-sm space-y-2.5">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Nombre</dt><dd class="font-semibold text-gray-900 text-right">{{ $usuario->nombres }} {{ $usuario->apellidos }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Documento</dt><dd class="text-gray-700 text-right">{{ $usuario->tipoDocumento->tipo_documento ?? 'C.C.' }} {{ $usuario->cedula }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Edad</dt><dd class="text-gray-700 text-right">{{ $usuario->edad ? $usuario->edad . ' años' : '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Ciudad</dt><dd class="text-gray-700 text-right">{{ $usuario->ciudad->ciudad ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Teléfono</dt><dd class="text-gray-700 text-right">{{ $usuario->telefono ?: '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Correo</dt><dd class="text-gray-700 text-right break-all">{{ $usuario->correo }}</dd></div>
            </dl>
        </div>
    </div>

@endsection
