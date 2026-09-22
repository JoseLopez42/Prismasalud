@extends('admin.layout')

@section('titulo', 'Editar rol')
@section('encabezado', 'Usuarios y Roles · Editar rol')

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

    <a href="{{ route('roles.index') }}" class="text-sm text-teal-600 hover:underline">
        ← Volver a roles
    </a>

    <div class="max-w-md mt-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center text-xl">🏷️</div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Editar rol</p>
                        <p class="text-xs text-white/70">#{{ $rol->id }} · {{ $rol->nombre }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Aviso: cambiar el nombre de un rol usado por Laravel
                     internamente (Administrador, Paciente, etc.) NO cambia
                     su id, así que el login y los permisos siguen funcionando. --}}
                <div class="bg-amber-50 text-amber-700 text-xs rounded-md px-4 py-2 mb-5">
                    ⚠️ Este rol se identifica internamente por su ID (#{{ $rol->id }}), no por el nombre.
                    Puedes renombrarlo sin afectar los permisos del sistema.
                </div>

                <form id="form-rol" method="POST" action="{{ route('roles.update', $rol->id) }}" class="space-y-5" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del rol</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-teal-50 flex items-center justify-center text-sm">🏷️</span>
                            <input type="text" name="nombre" value="{{ old('nombre', $rol->nombre) }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg pl-11 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                                   required autofocus>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('roles.index') }}"
                           class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar-rol"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton-rol">Guardar cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formRol = document.getElementById('form-rol');
        const botonGuardarRol = document.getElementById('boton-guardar-rol');
        const textoBotonRol = document.getElementById('texto-boton-rol');

        formRol.addEventListener('submit', function (evento) {
            const camposInvalidos = Array.from(formRol.querySelectorAll('.campo-form'))
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

            botonGuardarRol.disabled = true;
            textoBotonRol.innerHTML = '<span class="spinner"></span> Guardando...';
        });
    </script>

@endsection