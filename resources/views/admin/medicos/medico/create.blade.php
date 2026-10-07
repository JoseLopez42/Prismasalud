@extends('admin.layout')

@section('titulo', 'Nuevo médico')
@section('encabezado', 'Médicos y Consultorios · Nuevo médico')

@push('estilos')
<style>
    @keyframes aparecer {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .alerta-animada { animation: aparecer .25s ease; }

    @keyframes sacudir {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .campo-invalido { animation: sacudir .3s ease; border-color: #ef4444 !important; }

    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: girar .6s linear infinite;
    }
    @keyframes girar { to { transform: rotate(360deg); } }

    /* Recuadro de la foto elegida */
    .foto-preview { width: 72px; height: 72px; object-fit: cover; border-radius: 9999px; }
</style>
@endpush

@section('contenido')

    <a href="{{ route('medico.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a médicos
    </a>

    <div class="max-w-2xl mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg></div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Nuevo médico</p>
                        <p class="text-xs text-white/70">Datos profesionales y cuenta de acceso</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-medico" method="POST" action="{{ route('medico.store') }}" enctype="multipart/form-data" class="space-y-5" novalidate>
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula *</label>
                            <input type="text" name="cedula" id="campo-cedula" value="{{ old('cedula') }}"
                                   inputmode="numeric" maxlength="10"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: 1032456789" required autofocus>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Especialidad *</label>
                            <select name="id_especialidad"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                <option value="" disabled {{ old('id_especialidad') ? '' : 'selected' }}>Selecciona una especialidad...</option>
                                @foreach ($especialidades as $especialidad)
                                    <option value="{{ $especialidad->id }}" {{ old('id_especialidad') == $especialidad->id ? 'selected' : '' }}>
                                        {{ $especialidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres *</label>
                            <input type="text" name="nombres" value="{{ old('nombres') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: María Fernanda" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos *</label>
                            <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: Gómez Rivera" required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico *</label>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="doctora@ejemplo.com" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <input type="text" name="telefono" id="campo-telefono" value="{{ old('telefono') }}"
                                   inputmode="numeric" maxlength="15"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="3001234567">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                               placeholder="Calle 15 # 3-45, Barrio Centro">
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña de acceso *</label>
                            <input type="password" name="contrasena" minlength="6"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Mínimo 6 caracteres" required>
                            <p class="text-xs text-gray-400 mt-1">Usará su cédula y este correo para iniciar sesión.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="file" name="foto" id="campo-foto" accept="image/*"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2 text-sm shadow-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            <p class="text-xs text-gray-400 mt-1">JPG o PNG, máximo 2MB.</p>
                        </div>
                    </div>

                    <div id="zona-preview" class="hidden">
                        <img id="img-preview" class="foto-preview shadow-sm" alt="Vista previa">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('medico.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-medico"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-medico" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Crear médico</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // La cédula y el teléfono solo dejan dígitos
        ['campo-cedula', 'campo-telefono'].forEach(function (id) {
            const campo = document.getElementById(id);
            if (!campo) return;
            campo.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        });

        // Vista previa de la foto elegida
        const campoFoto = document.getElementById('campo-foto');
        const zonaPreview = document.getElementById('zona-preview');
        const imgPreview = document.getElementById('img-preview');

        campoFoto.addEventListener('change', function () {
            const archivo = this.files && this.files[0];
            if (!archivo) { zonaPreview.classList.add('hidden'); return; }
            imgPreview.src = URL.createObjectURL(archivo);
            zonaPreview.classList.remove('hidden');
        });

        // Validación visual + spinner
        const formMedico = document.getElementById('form-medico');
        const botonGuardarMedico = document.getElementById('boton-guardar-medico');
        const textoBotonMedico = document.getElementById('texto-boton-medico');

        formMedico.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formMedico.querySelectorAll('.campo-form'))
                .filter(campo => !campo.checkValidity());

            if (camposInvalidos.length > 0) {
                evento.preventDefault();
                camposInvalidos.forEach(campo => {
                    campo.classList.add('campo-invalido');
                    campo.addEventListener('animationend', () => campo.classList.remove('campo-invalido'), { once: true });
                });
                camposInvalidos[0].focus();
                return;
            }

            botonGuardarMedico.disabled = true;
            textoBotonMedico.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection
