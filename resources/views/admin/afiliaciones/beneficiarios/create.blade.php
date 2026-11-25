@extends('admin.layout')

@section('titulo', 'Nueva persona afiliada')
@section('encabezado', 'Afiliaciones · Nuevo cotizante')

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

    /* Tarjetas de selección Cotizante / Beneficiario */
    .opcion-tipo input { position: absolute; opacity: 0; pointer-events: none; }
    .opcion-tipo .tarjeta-tipo {
        border: 2px solid #e5e7eb; border-radius: .75rem; padding: .75rem 1rem;
        cursor: pointer; transition: all .15s ease; background: #fff;
    }
    .opcion-tipo:hover .tarjeta-tipo { border-color: #5eead4; }
    .opcion-tipo input:checked + .tarjeta-tipo { border-color: #0d9488; background: #f0fdfa; box-shadow: 0 0 0 3px rgba(13,148,136,.12); }
    .opcion-tipo input:focus-visible + .tarjeta-tipo { box-shadow: 0 0 0 3px rgba(13,148,136,.35); }
</style>
@endpush

@section('contenido')

    <a href="{{ route('beneficiarios.index') }}" class="text-sm text-teal-600 hover:underline">← Volver a beneficiarios</a>

    <div class="max-w-2xl mt-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <p class="font-bold text-lg leading-tight">Nueva persona afiliada</p>
                <p class="text-xs text-white/70">Registra al cotizante, con su cuenta de acceso. Los familiares beneficiarios se crean desde Afiliaciones.</p>
            </div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="alerta-animada bg-red-50 text-red-600 text-sm rounded-md px-4 py-2 mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-beneficiario" method="POST" action="{{ route('beneficiarios.store') }}" class="space-y-5" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Solo se registran cotizantes desde aquí; los beneficiarios familiares
                         se crean desde el módulo de Afiliaciones. --}}
                    <input type="hidden" name="tipo_persona" value="cotizante">

                    {{-- Solo si es beneficiario --}}
                    <div id="bloque-beneficiario" class="hidden space-y-5 bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Es familiar del cotizante</label>
                            <select id="campo-cotizante" name="cc_cotizante"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Seleccione...</option>
                                @foreach ($cotizantes as $c)
                                    <option value="{{ $c->cc_afiliado }}" data-eps="{{ $c->eps->nombre_eps ?? 'Sin EPS asignada' }}" {{ old('cc_cotizante') == $c->cc_afiliado ? 'selected' : '' }}>
                                        {{ $c->nombres }} {{ $c->apellidos }} ({{ $c->cc_afiliado }})
                                    </option>
                                @endforeach
                            </select>
                            <p id="error-cotizante" class="text-xs text-red-600 mt-1.5"></p>
                            <p id="eps-heredada" class="hidden text-sm text-teal-800 bg-teal-50 border border-teal-100 rounded-md px-3 py-2 mt-2">
                                EPS del beneficiario (la misma del cotizante): <strong id="eps-heredada-nombre"></strong>
                            </p>
                            @if ($cotizantes->isEmpty())
                                <p class="text-xs text-amber-700 mt-1.5">Aún no hay cotizantes registrados. Primero crea al cotizante.</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parentesco</label>
                            <select id="campo-parentesco" name="id_parentesco"
                                    class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                <option value="">Seleccione...</option>
                                @foreach ($parentescos as $p)
                                    <option value="{{ $p->id_parentesco }}" {{ (string) old('id_parentesco') === (string) $p->id_parentesco ? 'selected' : '' }}>
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="error-parentesco" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                    </div>

                    {{-- Solo si es cotizante: escoge su EPS (el beneficiario hereda la del cotizante) --}}
                    <div id="bloque-cotizante">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">EPS</label>
                        <select id="campo-eps" name="nit_eps"
                                class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            <option value="">Seleccione...</option>
                            @foreach ($eps as $e)
                                <option value="{{ $e->nit_eps }}" {{ old('nit_eps') == $e->nit_eps ? 'selected' : '' }}>
                                    {{ $e->nombre_eps }}
                                </option>
                            @endforeach
                        </select>
                        <p id="error-eps" class="text-xs text-red-600 mt-1.5"></p>
                    </div>

                    <div id="aviso-cuenta" class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-md px-4 py-3"></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
                        <input type="text" inputmode="numeric" id="campo-cc" name="cc_afiliado" value="{{ old('cc_afiliado') }}"
                               placeholder="Ej: 1000000001"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        <p id="error-cc" class="text-xs text-red-600 mt-1.5"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombres</label>
                            <input type="text" id="campo-nombres" name="nombres" value="{{ old('nombres') }}"
                                   placeholder="Ej: María Fernanda"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <p id="error-nombres" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellidos</label>
                            <input type="text" id="campo-apellidos" name="apellidos" value="{{ old('apellidos') }}"
                                   placeholder="Ej: Pérez Gómez"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <p id="error-apellidos" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Edad</label>
                            <input type="number" id="campo-edad" name="edad" value="{{ old('edad') }}"
                                   min="0" max="120" placeholder="Ej: 34"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <p id="error-edad" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                            <input type="tel" id="campo-telefono" name="telefono" value="{{ old('telefono') }}"
                                   placeholder="Ej: 300 123 4567"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <p id="error-telefono" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                        <input type="text" id="campo-direccion" name="direccion" value="{{ old('direccion') }}"
                               placeholder="Ej: Calle 12 # 34 - 56"
                               class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                        <p id="error-direccion" class="text-xs text-red-600 mt-1.5"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo</label>
                            <input type="email" id="campo-correo" name="correo" value="{{ old('correo') }}"
                                   placeholder="Ej: persona@correo.com"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <p id="error-correo" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
                            <input type="date" id="campo-fecha" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                                   class="campo-form w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            <p id="error-fecha" class="text-xs text-red-600 mt-1.5"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <input type="file" id="campo-foto" name="foto" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm shadow-sm bg-white file:mr-3 file:rounded-md file:border-0 file:bg-teal-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-teal-700 hover:file:bg-teal-100">
                        <p class="text-xs text-gray-400 mt-1">Imagen de máximo 2 MB.</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('beneficiarios.index') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" id="boton-guardar"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="texto-boton" class="inline-flex items-center gap-1.5">Registrar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form     = document.getElementById('form-beneficiario');
            const boton    = document.getElementById('boton-guardar');
            const texto    = document.getElementById('texto-boton');
            const bloque   = document.getElementById('bloque-beneficiario');
            const bloqueCot = document.getElementById('bloque-cotizante');
            const epsHeredada = document.getElementById('eps-heredada');
            const epsHeredadaNombre = document.getElementById('eps-heredada-nombre');
            const aviso    = document.getElementById('aviso-cuenta');
            const radios   = document.querySelectorAll('input[name="tipo_persona"]');

            const campos = {
                cotizante:  document.getElementById('campo-cotizante'),
                eps:        document.getElementById('campo-eps'),
                parentesco: document.getElementById('campo-parentesco'),
                cc:         document.getElementById('campo-cc'),
                nombres:    document.getElementById('campo-nombres'),
                apellidos:  document.getElementById('campo-apellidos'),
                edad:       document.getElementById('campo-edad'),
                telefono:   document.getElementById('campo-telefono'),
                direccion:  document.getElementById('campo-direccion'),
                correo:     document.getElementById('campo-correo'),
                fecha:      document.getElementById('campo-fecha'),
            };

            const errores = {
                cotizante:  document.getElementById('error-cotizante'),
                eps:        document.getElementById('error-eps'),
                parentesco: document.getElementById('error-parentesco'),
                cc:         document.getElementById('error-cc'),
                nombres:    document.getElementById('error-nombres'),
                apellidos:  document.getElementById('error-apellidos'),
                edad:       document.getElementById('error-edad'),
                telefono:   document.getElementById('error-telefono'),
                direccion:  document.getElementById('error-direccion'),
                correo:     document.getElementById('error-correo'),
                fecha:      document.getElementById('error-fecha'),
            };

            const soloLetras = /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s.'-]+$/;
            const telefonoValido = /^[+()\d\s-]{7,20}$/;
            const correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

            function tipoActual() {
                const marcado = document.querySelector('input[name="tipo_persona"]:checked');
                return marcado ? marcado.value : 'cotizante';
            }

            // Muestra/oculta los campos de beneficiario y cambia el aviso
            function actualizarTipo() {
                const esBeneficiario = tipoActual() === 'beneficiario';
                bloque.classList.toggle('hidden', !esBeneficiario);
                bloqueCot.classList.toggle('hidden', esBeneficiario);

                if (esBeneficiario) {
                    aviso.innerHTML = 'El beneficiario también tendrá <strong>su propia cuenta</strong> (usuario = cédula). ' +
                        'Entra con su cédula y su fecha de nacimiento y crea una contraseña <strong>corta y fácil de recordar</strong> ' +
                        '(mínimo 4 caracteres).';
                } else {
                    aviso.classList.add('hidden');
                }

                if (!esBeneficiario) {
                    limpiarError('cotizante');
                    limpiarError('parentesco');
                } else {
                    limpiarError('eps');
                }
                mostrarEpsHeredada();
            }

            // Beneficiario: muestra la EPS del cotizante elegido (no se escoge, se hereda)
            function mostrarEpsHeredada() {
                const opcion = campos.cotizante.options[campos.cotizante.selectedIndex];
                const hayEps = tipoActual() === 'beneficiario' && opcion && opcion.value !== '';
                epsHeredada.classList.toggle('hidden', !hayEps);
                if (hayEps) epsHeredadaNombre.textContent = opcion.getAttribute('data-eps');
            }
            campos.cotizante.addEventListener('change', mostrarEpsHeredada);

            function mostrarError(clave, mensaje) {
                const input = campos[clave];
                const error = errores[clave];
                error.textContent = mensaje;
                input.classList.remove('campo-invalido');
                void input.offsetWidth; // reinicia la animación de sacudida
                input.classList.add('campo-invalido');
            }

            function limpiarError(clave) {
                errores[clave].textContent = '';
                campos[clave].classList.remove('campo-invalido');
            }

            function edadDesdeFecha(fecha) {
                const hoy = new Date();
                const nac = new Date(fecha);
                let edad = hoy.getFullYear() - nac.getFullYear();
                const mes = hoy.getMonth() - nac.getMonth();
                if (mes < 0 || (mes === 0 && hoy.getDate() < nac.getDate())) {
                    edad--;
                }
                return edad;
            }

            // Valida UN campo y devuelve true si está bien
            function validarCampo(clave) {
                const valor = campos[clave].value.trim();

                switch (clave) {
                    case 'cotizante':
                        if (tipoActual() !== 'beneficiario') break;
                        if (!valor) return mostrarError('cotizante', 'Selecciona de qué cotizante es familiar.'), false;
                        break;

                    case 'eps':
                        if (tipoActual() !== 'cotizante') break;
                        if (!valor) return mostrarError('eps', 'Selecciona la EPS del cotizante.'), false;
                        break;

                    case 'parentesco':
                        if (tipoActual() !== 'beneficiario') break;
                        if (!valor) return mostrarError('parentesco', 'Selecciona el parentesco con el cotizante.'), false;
                        break;

                    case 'cc':
                        if (!valor) return mostrarError('cc', 'La cédula es obligatoria.'), false;
                        if (!/^\d{4,12}$/.test(valor)) return mostrarError('cc', 'La cédula debe tener entre 4 y 12 dígitos numéricos.'), false;
                        break;

                    case 'nombres':
                        if (!valor) return mostrarError('nombres', 'Los nombres son obligatorios.'), false;
                        if (valor.length > 100) return mostrarError('nombres', 'Los nombres no pueden superar 100 caracteres.'), false;
                        if (!soloLetras.test(valor)) return mostrarError('nombres', 'Solo se permiten letras, espacios, puntos, guiones y apóstrofos.'), false;
                        break;

                    case 'apellidos':
                        if (!valor) return mostrarError('apellidos', 'Los apellidos son obligatorios.'), false;
                        if (valor.length > 100) return mostrarError('apellidos', 'Los apellidos no pueden superar 100 caracteres.'), false;
                        if (!soloLetras.test(valor)) return mostrarError('apellidos', 'Solo se permiten letras, espacios, puntos, guiones y apóstrofos.'), false;
                        break;

                    case 'edad':
                        if (!valor) return mostrarError('edad', 'La edad es obligatoria.'), false;
                        if (!/^\d+$/.test(valor)) return mostrarError('edad', 'La edad debe ser un número entero.'), false;
                        const edadNum = parseInt(valor, 10);
                        if (edadNum < 0 || edadNum > 120) return mostrarError('edad', 'La edad debe estar entre 0 y 120 años.'), false;
                        if (campos.fecha.value) {
                            const edadReal = edadDesdeFecha(campos.fecha.value);
                            if (Math.abs(edadNum - edadReal) > 1) {
                                return mostrarError('edad', 'La edad no coincide con la fecha de nacimiento (según la fecha tiene ' + edadReal + ' años).'), false;
                            }
                        }
                        break;

                    case 'telefono':
                        if (!valor) return mostrarError('telefono', 'El teléfono es obligatorio.'), false;
                        if (!telefonoValido.test(valor)) return mostrarError('telefono', 'Teléfono inválido: 7 a 20 caracteres (números, +, espacios, paréntesis o guiones).'), false;
                        break;

                    case 'direccion':
                        if (!valor) return mostrarError('direccion', 'La dirección es obligatoria.'), false;
                        if (valor.length > 255) return mostrarError('direccion', 'La dirección no puede superar 255 caracteres.'), false;
                        break;

                    case 'correo':
                        if (!valor) return mostrarError('correo', 'El correo es obligatorio.'), false;
                        if (valor.length > 100) return mostrarError('correo', 'El correo no puede superar 100 caracteres.'), false;
                        if (!correoValido.test(valor)) return mostrarError('correo', 'El correo no tiene un formato válido.'), false;
                        break;

                    case 'fecha':
                        if (!valor) return mostrarError('fecha', 'La fecha de nacimiento es obligatoria.'), false;
                        const fechaSel = new Date(valor);
                        if (isNaN(fechaSel.getTime())) return mostrarError('fecha', 'La fecha de nacimiento no es válida.'), false;
                        if (fechaSel > new Date()) return mostrarError('fecha', 'La fecha de nacimiento no puede ser futura.'), false;
                        break;
                }

                limpiarError(clave);
                return true;
            }

            // Validación en vivo: al salir del campo
            Object.keys(campos).forEach(clave => {
                campos[clave].addEventListener('blur', () => validarCampo(clave));
                const evento = campos[clave].tagName === 'SELECT' ? 'change' : 'input';
                campos[clave].addEventListener(evento, () => {
                    if (errores[clave].textContent !== '') {
                        validarCampo(clave);
                    }
                });
            });

            radios.forEach(r => r.addEventListener('change', actualizarTipo));
            actualizarTipo();

            // Validación completa al enviar
            form.addEventListener('submit', function (evento) {
                const orden = ['cotizante', 'parentesco', 'eps', 'cc', 'nombres', 'apellidos', 'edad', 'telefono', 'direccion', 'correo', 'fecha'];
                const resultados = orden.map(clave => validarCampo(clave));

                if (resultados.includes(false)) {
                    evento.preventDefault();
                    const primerFallo = orden[resultados.indexOf(false)];
                    campos[primerFallo].focus();
                    return;
                }

                boton.disabled = true;
                texto.innerHTML = '<span class="spinner"></span> Guardando...';
            });
        })();
    </script>

@endsection