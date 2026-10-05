{{--
    =====================================================================
    VISTA: login.blade.php
    Formulario de inicio de sesión. Pide cédula y contraseña, los
    envía por POST a LoginController@iniciarSesion.
    Muestra el error si las credenciales son incorrectas, o si el
    middleware redirigió aquí por falta de permisos.
    =====================================================================
--}}
@extends('partials.auth')

@section('titulo', 'Iniciar sesión - PrismaSalud IPS')
@section('card_titulo', 'Iniciar sesión')
@section('card_descripcion', 'Ingresa con tu cédula y contraseña')

@push('estilos')
<style>
    /* Modal de términos y condiciones */
    #modal-terminos {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transition: opacity .2s ease, visibility .2s ease;
    }
    #modal-terminos.abierto {
        opacity: 1;
        visibility: visible;
    }
    #modal-terminos .tarjeta-modal {
        transform: scale(.95);
        transition: transform .2s ease;
    }
    #modal-terminos.abierto .tarjeta-modal {
        transform: scale(1);
    }
</style>
@endpush

@section('formulario')

    {{-- Mensaje de error: credenciales incorrectas o sin permiso --}}
    @if ($errors->has('login'))
        <div class="flex items-start gap-2 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first('login') }}</span>
        </div>
    @endif

    {{-- Error enviado con ->with('error', ...) desde el middleware --}}
    @if (session('error'))
        <div class="flex items-start gap-2 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        <div>
            <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    @include('admin.partials.icono', ['icono' => 'perfil', 'clase' => 'w-5 h-5'])
                </div>
                <input type="number" name="cedula" id="cedula" value="{{ old('cedula') }}" autocomplete="username"
                       placeholder="Tu número de identificación"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                       required autofocus>
            </div>
        </div>

        <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    @include('admin.partials.icono', ['icono' => 'candado', 'clase' => 'w-5 h-5'])
                </div>
                <input type="password" name="contrasena" id="contrasena" autocomplete="current-password"
                       placeholder="Tu contraseña"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2.5 text-sm bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none transition"
                       required>
                <button type="button" onclick="mostrarContrasena('contrasena', this)"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-teal-600" aria-label="Mostrar contraseña">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label class="flex items-center gap-3 border-2 border-teal-200 bg-teal-50/70 rounded-xl px-4 py-3 cursor-pointer select-none transition hover:border-teal-400 hover:bg-teal-50">
                <span class="w-9 h-9 rounded-lg bg-white border border-teal-200 text-teal-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <input type="checkbox" name="terminos" id="checkbox-terminos" value="1"
                       class="w-4 h-4 text-teal-600 border-teal-300 rounded focus:ring-teal-500 accent-teal-600" required>
                <span class="text-xs leading-snug text-gray-700">
                    He leído y acepto los
                    <button type="button" onclick="abrirTerminos()"
                            class="text-teal-700 hover:underline font-semibold">
                        Términos y Condiciones
                    </button>.
                </span>
            </label>
            @error('terminos')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold py-2.5 rounded-lg hover:from-green-700 hover:to-teal-700 hover:shadow-lg transition active:scale-[.99]">
            Iniciar sesión
        </button>

        <a href="{{ route('recuperar.solicitar.form') }}"
           class="block text-center text-sm text-teal-600 hover:underline font-medium">
            ¿Olvidaste tu contraseña?
        </a>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-teal-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al inicio
        </a>

        <div class="mt-3 flex items-center justify-center gap-3 text-xs text-gray-400">
            <span>© {{ date('Y') }} PrismaSalud IPS</span>
            <span class="opacity-40">•</span>
            <a href="{{ route('terminos.index') }}" class="hover:text-teal-600 transition">Términos y Condiciones</a>
            <span class="opacity-40">•</span>
            <a href="{{ route('privacidad.index') }}" class="hover:text-teal-600 transition">Política de Privacidad</a>
        </div>
    </div>

    {{-- ===================================================
         MODAL DE TÉRMINOS Y CONDICIONES
    =================================================== --}}
    <div id="modal-terminos">
        <div class="tarjeta-modal bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden flex flex-col max-h-[85vh]">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-7 h-7 object-contain">
                    <h2 class="text-base font-bold text-gray-900">Términos y Condiciones</h2>
                </div>
                <button type="button" onclick="cerrarTerminos()" aria-label="Cerrar"
                        class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 overflow-y-auto text-sm text-gray-600 space-y-5 leading-relaxed">

                <p>
                    Estos Términos y Condiciones regulan el acceso y uso de la plataforma web
                    y los servicios de <strong>PrismaSalud IPS</strong>, Institución Prestadora de
                    Servicios de Salud. Al aceptarlos, declaras haber leído y estar de acuerdo con el
                    tratamiento de tus datos conforme a la <strong>Ley 1581 de 2012</strong> (protección
                    de datos personales) y con la prestación de servicios bajo el marco del
                    <strong>Sistema General de Seguridad Social en Salud (Ley 100 de 1993)</strong> y la
                    <strong>Ley 1751 de 2015</strong> (Ley Estatutaria de Salud).
                </p>
                <p class="text-xs text-gray-500">
                    Puedes consultar la versión completa en
                    <a href="{{ route('terminos.index') }}" target="_blank" class="text-teal-700 hover:underline font-semibold">Términos y Condiciones</a>
                    y en
                    <a href="{{ route('privacidad.index') }}" target="_blank" class="text-teal-700 hover:underline font-semibold">Política de Privacidad</a>.
                </p>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">1. Naturaleza de los servicios</h3>
                    <p>PrismaSalud IPS presta servicios de promoción, prevención, diagnóstico,
                    tratamiento y rehabilitación en salud, a través de sus sedes físicas y de este
                    portal para pacientes y usuarios del sistema.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">2. Datos personales, habeas data y confidencialidad</h3>
                    <p>Tus datos personales — incluyendo los datos de salud, de carácter sensible — son
                    tratados conforme a la <strong>Ley 1581 de 2012</strong>, el <strong>Decreto 1377 de
                    2013</strong> y la jurisprudencia constitucional. La autorización es previa, expresa e
                    informada, y podrá ser revocada cuando no exista deber legal de conservarla. La
                    información se utilizará únicamente para fines asistenciales, administrativos y de
                    facturación, y no será revelada a terceros sin tu autorización, salvo los casos
                    previstos por la ley. Conoce tu política completa y tus derechos como titular en
                    nuestra Política de Privacidad.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">3. Historia clínica</h3>
                    <p>La historia clínica es un documento privado, sometido a reserva, regulado por la
                    <strong>Resolución 1995 de 1999</strong>. Solo puede ser consultado por el personal de
                    salud autorizado, por el propio paciente o por las entidades que la ley habilite.
                    El acceso queda registrado y auditado por la institución, y es custodiado por un
                    término mínimo de veinte (20) años.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">4. Uso correcto de la cuenta</h3>
                    <p>Tu cuenta es personal e intransferible. Debes mantener la confidencialidad de tu
                    contraseña y notificar de inmediato cualquier uso no autorizado. La cédula y los
                    datos registrados deben ser veraces y actualizados.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">5. Citas y agendamiento</h3>
                    <p>Las citas pueden agendarse, consultarse o cancelarse a través del portal. Te
                    recomendamos llegar puntualmente a tu cita; en caso de no poder asistir, cancélala
                    con suficiente antelación para asignar el espacio a otro usuario.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">6. Medicamentos y prescripciones</h3>
                    <p>Los medicamentos solo pueden ser solicitados y entregados con una prescripción
                    médica válida. La automedicación es riesgosa; cualquier duda sobre un tratamiento
                    debe consultarse con el profesional de salud.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">7. Procedimientos y consentimiento informado</h3>
                    <p>Todo procedimiento invasivo, quirúrgico o diagnóstico requiere el consentimiento
                    informado del paciente o su representante legal (Resolución 8430 de 1993), salvo
                    situaciones de urgencia que pongan en riesgo la vida. Como paciente tienes derecho a
                    ser informado sobre el diagnóstico, tratamiento, riesgos y alternativas, conforme a la
                    Resolución 13437 de 1991 y la Ley 1751 de 2015.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">8. Pagos, copagos y cuotas moderadoras</h3>
                    <p>Los valores correspondientes a copagos, cuotas moderadoras o servicios no
                    cubiertos serán informados antes de la prestación del servicio y podrán cancelarse
                    por los medios habilitados por la institución.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">9. Comunicaciones y notificaciones</h3>
                    <p>Podremos enviarte recordatorios de citas, resultados y avisos importantes a través
                    del correo electrónico, mensajes de texto o llamadas, usando los datos de contacto
                    que registres en tu perfil.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">10. Obligaciones del usuario</h3>
                    <p>Debes proporcionar información veraz, asistir a tus citas, seguir las indicaciones
                    médicas, no causar daño a la infraestructura ni al personal, y respetar las normas
                    de convivencia dentro de las sedes.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">11. Suspensión del acceso</h3>
                    <p>PrismaSalud IPS podrá suspender o revocar el acceso a la plataforma si se detecta
                    un uso indebido, fraude, suplantación de identidad o incumplimiento de estos términos,
                    sin perjuicio de las acciones legales que correspondan.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">12. Modificaciones de los términos</h3>
                    <p>Nos reservamos el derecho de actualizar estos términos cuando las necesidades del
                    servicio o la normativa lo requieran. La versión vigente estará siempre publicada en
                    este portal y los cambios se te notificarán por los canales habilitados.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-1">13. Ley aplicable y jurisdicción</h3>
                    <p>Estos términos se rigen por las leyes de la República de Colombia y cualquier
                    controversia será resuelta ante los jueces competentes de la ciudad de Ibagué, Tolima.
                    La plataforma y los servicios se prestan bajo el siguiente marco normativo:</p>
                    <ul class="list-disc list-inside space-y-1 mt-2 text-xs text-gray-500">
                        <li>Ley 100 de 1993 — Sistema General de Seguridad Social en Salud.</li>
                        <li>Ley 1751 de 2015 — Ley Estatutaria de Salud.</li>
                        <li>Ley 1581 de 2012 y Decreto 1377 de 2013 — Protección de Datos Personales.</li>
                        <li>Resolución 1995 de 1999 — Normas de la Historia Clínica.</li>
                        <li>Resolución 13437 de 1991 — Derechos y Deberes del Paciente.</li>
                        <li>Resolución 8430 de 1993 — Investigación en salud y consentimiento informado.</li>
                        <li>Ley 23 de 1981 — Ética del ejercicio de la medicina.</li>
                        <li>Decreto 780 de 2016 — Decreto Único Reglamentario del Sector Salud.</li>
                        <li>Ley 1755 de 2015 — Derecho de petición (PQRS).</li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 px-6 py-4 border-t border-gray-200">
                <button type="button" onclick="cerrarTerminos()"
                        class="flex-1 border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="button" id="boton-aceptar-terminos"
                        class="flex-1 bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 transition">
                    Entendido
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const modalTerminos = document.getElementById('modal-terminos');

    function abrirTerminos() {
        modalTerminos.classList.add('abierto');
    }

    function cerrarTerminos() {
        modalTerminos.classList.remove('abierto');
    }

    document.getElementById('boton-aceptar-terminos').addEventListener('click', function () {
        cerrarTerminos();
    });

    modalTerminos.addEventListener('click', function (evento) {
        if (evento.target.id === 'modal-terminos') {
            cerrarTerminos();
        }
    });

    // Mostrar / ocultar la contraseña
    function mostrarContrasena(idInput, boton) {
        const campo = document.getElementById(idInput);
        const ver = campo.type === 'password';
        campo.type = ver ? 'text' : 'password';
        boton.innerHTML = ver
            ? '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>';
    }
</script>
@endpush