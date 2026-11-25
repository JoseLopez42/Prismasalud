@extends('admin.layout')

@section('titulo', $beneficiario->esCotizante() ? 'Editar cotizante' : 'Editar beneficiario')
@section('encabezado', 'Afiliaciones · ' . ($beneficiario->esCotizante() ? 'Editar cotizante' : 'Editar beneficiario'))

@section('contenido')

    @php
        $esCotizante = $beneficiario->esCotizante();
        $titulo = $esCotizante ? 'Editar cotizante' : 'Editar beneficiario';
    @endphp

    <a href="{{ route('beneficiarios.index') }}" class="text-sm text-teal-600 hover:underline">← Volver al listado de afiliados</a>

    <div class="max-w-2xl mt-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ $titulo }}</p>
                        <p class="text-xs text-white/70">{{ $beneficiario->cc_afiliado }} · {{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full border border-white/40 bg-white/15">
                        {{ $esCotizante ? 'Cotizante (titular)' : 'Beneficiario (familiar)' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('beneficiarios.update', $beneficiario->cc_afiliado) }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Datos propios de cada tipo de persona --}}
                    @if ($esCotizante)
                        <div class="bg-teal-50 border border-teal-100 rounded-xl p-4 space-y-3">
                            <p class="text-sm font-semibold text-teal-900">Datos del cotizante</p>
                            <p class="text-xs text-teal-800">Es el titular de la afiliación: no depende de otro afiliado y no tiene parentesco asignado.</p>
                            <div>
                                <label for="nit_eps" class="block text-sm font-medium text-gray-700 mb-1.5">EPS</label>
                                <select id="nit_eps" name="nit_eps"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                    <option value="">Sin EPS asignada</option>
                                    @foreach ($eps as $e)
                                        <option value="{{ $e->nit_eps }}" {{ old('nit_eps', $beneficiario->nit_eps) == $e->nit_eps ? 'selected' : '' }}>
                                            {{ $e->nombre_eps }}{{ $e->activo ? '' : ' (inactiva)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nit_eps')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1.5">Al cambiarla, también se actualizará la EPS de sus beneficiarios.</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 space-y-3">
                            <p class="text-sm font-semibold text-indigo-900">Datos del beneficiario</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Familiar de</label>
                                    <input type="text" disabled
                                           value="{{ $beneficiario->cotizante?->nombres }} {{ $beneficiario->cotizante?->apellidos }} ({{ $beneficiario->cc_cotizante ?? '—' }})"
                                           class="w-full border border-gray-200 bg-white/70 text-gray-500 rounded-lg px-3 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">EPS heredada</label>
                                    <input type="text" disabled
                                           value="{{ $beneficiario->eps?->nombre_eps ?? 'Sin EPS asignada' }}"
                                           class="w-full border border-gray-200 bg-white/70 text-gray-500 rounded-lg px-3 py-2.5 text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="parentesco" class="block text-sm font-medium text-gray-700 mb-1.5">Parentesco</label>
                                <select id="parentesco" name="id_parentesco"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                        required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($parentescos as $p)
                                        <option value="{{ $p->id_parentesco }}" {{ (string) old('id_parentesco', $beneficiario->id_parentesco) === (string) $p->id_parentesco ? 'selected' : '' }}>
                                            {{ $p->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_parentesco')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1.5">La EPS se hereda del cotizante; para cambiarla, edita la ficha del cotizante.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Foto de perfil --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto de perfil</label>
                        @if ($beneficiario->usuario && $beneficiario->usuario->foto)
                            <img src="{{ asset('storage/' . $beneficiario->usuario->foto) }}"
                                 alt="Foto actual"
                                 class="w-16 h-16 rounded-full object-cover mb-2 border border-gray-200">
                        @endif
                        <input type="file" name="foto" accept="image/*"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-teal-50 file:text-teal-700 file:font-semibold hover:file:bg-teal-100">
                        <p class="text-xs text-gray-400 mt-1">Déjalo vacío para conservar la foto actual.</p>
                    </div>

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
                    <div>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $beneficiario->fecha_nacimiento) }}" min="1900-01-01" max="{{ now()->toDateString() }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
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
