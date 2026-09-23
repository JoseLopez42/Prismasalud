@extends('admin.layout')

@section('titulo', 'Editar EPS')
@section('encabezado', 'EPS · Editar EPS')

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
</style>
@endpush

@section('contenido')

    <a href="{{ route('eps.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a EPS
    </a>

    <div class="max-w-lg mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Editar EPS</p>
                        <p class="text-xs text-white/70">NIT {{ $eps->nit_eps }} · {{ $eps->nombre_eps }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-eps" method="POST" action="{{ route('eps.update', $eps->nit_eps) }}" class="space-y-5" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">NIT</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></span>
                            <input type="text" value="{{ $eps->nit_eps }}"
                                   class="w-full border border-gray-200 bg-gray-50 text-gray-500 rounded-lg pl-11 pr-3 py-2.5 text-sm cursor-not-allowed"
                                   disabled>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">El NIT es el identificador de la EPS y no se puede modificar.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de la EPS</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></span>
                            <input type="text" name="nombre_eps" value="{{ old('nombre_eps', $eps->nombre_eps) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required autofocus>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono de contacto</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></span>
                                <input type="text" name="telefono_contacto" id="campo-telefono" value="{{ old('telefono_contacto', $eps->telefono_contacto) }}"
                                       inputmode="numeric" maxlength="10"
                                       class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                       required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Sede</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></span>
                            <select name="id_sede"
                                    class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white transition"
                                    required>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id_sede }}"
                                        {{ old('id_sede', $eps->id_sede) == $sede->id_sede ? 'selected' : '' }}>
                                        {{ $sede->nombre_sede }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('eps.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-eps"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-eps" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Guardar cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const campoTelefono = document.getElementById('campo-telefono');
        campoTelefono.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });

        const formEps = document.getElementById('form-eps');
        const botonGuardarEps = document.getElementById('boton-guardar-eps');
        const textoBotonEps = document.getElementById('texto-boton-eps');

        formEps.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formEps.querySelectorAll('.campo-form'))
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

            botonGuardarEps.disabled = true;
            textoBotonEps.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection