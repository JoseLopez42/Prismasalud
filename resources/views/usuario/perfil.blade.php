@extends('usuario.layout')

@section('titulo', 'Mi perfil')
@section('encabezado', 'Mi perfil')

@section('contenido')

    @php
        $iniciales = strtoupper(substr($usuario->nombres, 0, 1) . substr($usuario->apellidos, 0, 1));
    @endphp

    <div class="mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Mi perfil</h2>
        <p class="text-sm text-gray-500">Tus datos personales y de contacto</p>
    </div>

    <div class="max-w-3xl">
        {{-- Encabezado --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white flex items-center justify-center text-xl font-bold flex-shrink-0">
                    {{ $iniciales }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-lg font-bold text-gray-900 truncate">{{ $usuario->nombres }} {{ $usuario->apellidos }}</p>
                    <p class="text-sm text-gray-500">{{ $usuario->tipoUsuario->nombre ?? 'Paciente' }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $usuario->activo ? 'text-green-700 bg-green-50' : 'text-gray-600 bg-gray-100' }} px-2.5 py-1 rounded-full flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full {{ $usuario->activo ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-5 text-sm pt-6">
                <div>
                    <dt class="text-gray-500 mb-0.5">Documento</dt>
                    <dd class="font-semibold text-gray-900">{{ $usuario->tipoDocumento->tipo_documento ?? 'C.C.' }} {{ $usuario->cedula }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Edad</dt>
                    <dd class="font-semibold text-gray-900">{{ $usuario->edad ? $usuario->edad . ' años' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Correo</dt>
                    <dd class="font-semibold text-gray-900 break-all">{{ $usuario->correo }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Teléfono</dt>
                    <dd class="font-semibold text-gray-900">{{ $usuario->telefono ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Ciudad</dt>
                    <dd class="font-semibold text-gray-900">{{ $usuario->ciudad->ciudad ?? '—' }}</dd>
                </div>
            </dl>

            <p class="text-xs text-gray-400 mt-6 border-t border-gray-100 pt-4">
                Para actualizar tus datos personales, comunícate con la IPS.
            </p>
        </div>
    </div>

@endsection
