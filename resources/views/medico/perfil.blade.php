@extends('medico.layout')

@section('titulo', 'Mi perfil')
@section('encabezado', 'Mi perfil')

@push('estilos')
<style>
    .foto-perfil { width: 96px; height: 96px; object-fit: cover; border-radius: 9999px; }
    .foto-preview { width: 96px; height: 96px; object-fit: cover; border-radius: 9999px; }
</style>
@endpush

@section('contenido')

    @php
        $iniciales = strtoupper(substr($medico->nombres ?? 'M', 0, 1) . substr($medico->apellidos ?? '', 0, 1));
    @endphp

    @if (session('exito'))
        <div class="bg-green-50 text-green-700 text-sm rounded-md px-4 py-2 mb-4">
            {{ session('exito') }}
        </div>
    @endif

    {{-- Encabezado del perfil --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1A5C3A] to-[#2E8B57] text-white p-6 mb-6">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex-shrink-0">
                @if ($medico->foto)
                    <img src="{{ asset('storage/' . $medico->foto) }}" alt="Foto de perfil"
                         class="foto-perfil ring-4 ring-white/20">
                @else
                    <div class="foto-perfil bg-white/15 ring-4 ring-white/20 flex items-center justify-center text-3xl font-bold">
                        {{ $iniciales }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-white/70 text-sm">Cédula {{ $medico->cedula }}</p>
                <h2 class="text-xl md:text-2xl font-bold mt-0.5">Dr(a). {{ $medico->nombres }} {{ $medico->apellidos }}</h2>
                <p class="text-white/80 text-sm mt-1">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }}</p>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-2 top-16 w-24 h-24 rounded-full bg-white/5"></div>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Datos personales</h3>
                <p class="text-sm text-gray-500">Actualiza tu información básica y tu foto de perfil</p>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('medico.perfil.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Foto --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            @if ($medico->foto)
                                <img id="img-actual" src="{{ asset('storage/' . $medico->foto) }}" class="foto-preview border border-gray-200" alt="Foto actual">
                            @else
                                <div id="img-actual" class="foto-preview bg-teal-50 border border-teal-100 flex items-center justify-center text-2xl font-bold text-teal-700">
                                    {{ $iniciales }}
                                </div>
                            @endif
                            <div id="zona-preview" class="hidden">
                                <img id="img-preview" class="foto-preview border border-gray-200" alt="Vista previa">
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto de perfil</label>
                            <input type="file" name="foto" id="campo-foto" accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm shadow-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            <p class="text-xs text-gray-400 mt-1">JPG o PNG, máximo 2MB.</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres *</label>
                            <input type="text" name="nombres" value="{{ old('nombres', $medico->nombres) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required autofocus>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos *</label>
                            <input type="text" name="apellidos" value="{{ old('apellidos', $medico->apellidos) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico *</label>
                            <input type="email" name="correo" value="{{ old('correo', $medico->correo) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <input type="text" name="telefono" id="campo-telefono" value="{{ old('telefono', $medico->telefono) }}"
                                   inputmode="numeric" maxlength="15"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="3001234567">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $medico->direccion) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                               placeholder="Calle 15 # 3-45, Barrio Centro">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <input type="password" name="contrasena" minlength="6"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                               placeholder="Déjala vacía para conservar la actual">
                        <p class="text-xs text-gray-400 mt-1">Mínimo 6 caracteres si decides cambiarla.</p>
                    </div>

                    <div class="flex items-start gap-2 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                        @include('admin.partials.icono', ['icono' => 'candado', 'clase' => 'w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5'])
                        <span>La cédula ({{ $medico->cedula }}) y la especialidad ({{ $medico->especialidad->nombre ?? '—' }}) las administra la IPS y no se pueden modificar desde aquí.</span>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('medico.inicio') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const campoTelefono = document.getElementById('campo-telefono');
    if (campoTelefono) {
        campoTelefono.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    const campoFoto = document.getElementById('campo-foto');
    const zonaPreview = document.getElementById('zona-preview');
    const imgPreview = document.getElementById('img-preview');
    const imgActual = document.getElementById('img-actual');

    campoFoto.addEventListener('change', function () {
        const archivo = this.files && this.files[0];
        if (!archivo) { zonaPreview.classList.add('hidden'); imgActual.classList.remove('hidden'); return; }
        imgPreview.src = URL.createObjectURL(archivo);
        zonaPreview.classList.remove('hidden');
        imgActual.classList.add('hidden');
    });
</script>
@endpush
