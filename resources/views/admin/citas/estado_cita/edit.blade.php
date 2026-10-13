@extends('admin.layout')

@section('titulo', 'Editar estado de cita')
@section('encabezado', 'Citas Médicas · Editar estado de cita')

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

    <a href="{{ route('estado_cita.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a estados de cita
    </a>

    <div class="max-w-lg mt-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Editar estado de cita</p>
                        <p class="text-xs text-white/70">{{ $estado->nombre }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-estado-cita" method="POST" action="{{ route('estado_cita.update', $estado->id) }}" class="space-y-5" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del estado</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            <input type="text" name="nombre" value="{{ old('nombre', $estado->nombre) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   placeholder="Ej: Pendiente" required autofocus>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea name="descripcion" rows="3"
                                  class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                  placeholder="Breve descripción del estado...">{{ old('descripcion', $estado->descripcion) }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('estado_cita.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-estado-cita"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-estado-cita" class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Guardar cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formEstadoCita = document.getElementById('form-estado-cita');
        const botonGuardarEstadoCita = document.getElementById('boton-guardar-estado-cita');
        const textoBotonEstadoCita = document.getElementById('texto-boton-estado-cita');

        formEstadoCita.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formEstadoCita.querySelectorAll('.campo-form'))
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

            botonGuardarEstadoCita.disabled = true;
            textoBotonEstadoCita.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection