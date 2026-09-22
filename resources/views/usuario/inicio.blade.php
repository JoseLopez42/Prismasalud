{{-- Inicio del panel del Usuario: bienvenida y resumen básico --}}
@extends('usuario.layout')

@section('titulo', 'Inicio')
@section('encabezado', 'Panel del Usuario')

@section('contenido')

    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">
        Hola, {{ $usuario->nombres }} {{ $usuario->apellidos }}
    </h2>
    <p class="text-sm text-gray-500 mb-6">Bienvenido a tu espacio en PrismaSalud IPS · hoy</p>

    {{-- Resumen rápido con datos de ejemplo por ahora --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach ($resumen as $item)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 mb-3">
                    @include('admin.partials.icono', ['icono' => $item['icono'], 'clase' => 'w-5 h-5'])
                </div>
                <p class="text-2xl font-extrabold text-gray-900">{{ $item['numero'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['texto'] }}</p>
            </div>
        @endforeach
    </div>

    <h3 class="text-base font-bold text-gray-900 mb-1">Servicios disponibles</h3>
    <p class="text-sm text-gray-500 mb-4">Acceso rápido a los servicios que puedes gestionar desde tu cuenta</p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($servicios as $servicio)
            <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-teal-200 transition">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 flex-shrink-0">
                        @include('admin.partials.icono', ['icono' => $servicio['icono'], 'clase' => 'w-5 h-5'])
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $servicio['titulo'] }}</p>
                </div>
                <p class="text-xs text-gray-500 mb-4">{{ $servicio['descripcion'] }}</p>
                <a href="#"
                   class="group inline-flex items-center gap-1 text-xs font-medium text-teal-700 bg-teal-50 border border-teal-100 px-3 py-1.5 rounded-full hover:bg-teal-600 hover:text-white hover:border-teal-600 hover:shadow-md transition">
                    Consultar
                    @include('admin.partials.icono', ['icono' => 'flecha', 'clase' => 'w-3 h-3 opacity-0 group-hover:opacity-100'])
                </a>
            </div>
        @endforeach
    </div>

@endsection