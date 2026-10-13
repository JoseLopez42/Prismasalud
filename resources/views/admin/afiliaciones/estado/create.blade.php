@extends('admin.layout')

@section('titulo', 'Nuevo estado')
@section('encabezado', 'Afiliaciones · Nuevo estado')

@section('contenido')

    <a href="{{ route('estados.index') }}" class="text-sm text-teal-600 hover:underline">← Volver a estados</a>

    <div class="max-w-lg mt-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <p class="font-bold text-lg leading-tight">Nuevo estado</p>
            </div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('estados.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del estado</label>
                        <input type="text" name="estado" value="{{ old('estado') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('estados.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">Cancelar</a>
                        <button type="submit" class="flex-1 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">Crear estado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
