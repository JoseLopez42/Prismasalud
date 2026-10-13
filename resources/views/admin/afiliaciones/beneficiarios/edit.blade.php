@extends('admin.layout')

@section('titulo', 'Editar beneficiario')
@section('encabezado', 'Afiliaciones · Editar beneficiario')

@section('contenido')

    <a href="{{ route('beneficiarios.index') }}" class="text-sm text-teal-600 hover:underline">← Volver a beneficiarios</a>

    <div class="max-w-2xl mt-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <p class="font-bold text-lg leading-tight">Editar beneficiario</p>
                <p class="text-xs text-white/70">{{ $beneficiario->cc_afiliado }} · {{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</p>
            </div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('beneficiarios.update', $beneficiario->cc_afiliado) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                            <input type="text" name="nombres" value="{{ old('nombres', $beneficiario->nombres) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                            <input type="text" name="apellidos" value="{{ old('apellidos', $beneficiario->apellidos) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Edad</label>
                            <input type="number" name="edad" value="{{ old('edad', $beneficiario->edad) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $beneficiario->telefono) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $beneficiario->direccion) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo</label>
                            <input type="email" name="correo" value="{{ old('correo', $beneficiario->correo) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $beneficiario->fecha_nacimiento) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('beneficiarios.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">Cancelar</a>
                        <button type="submit" class="flex-1 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
