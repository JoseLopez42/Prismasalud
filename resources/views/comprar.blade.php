<!-- comprar.blade.php - Formulario de compra de un plan PrismaSalud -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar {{ $plan['nombre'] }} - PrismaSalud IPS</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --azul: #1B4D6B;
            --azul-claro: #2E86AB;
            --naranja: #0C7B93;
            --teal: #12A594;
            --teal-claro: #4ADE80;
        }

        body { font-family: 'Inter', sans-serif; }

        .site-header {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .paso {
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            background: #ffffff;
            padding: 1.5rem;
        }

        .paso-numero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: #ccfbf1;
            color: #0f766e;
            font-weight: 800;
            font-size: .85rem;
        }

        .campo label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: .3rem;
        }

        .campo input,
        .campo select {
            width: 100%;
            padding: .65rem .85rem;
            border: 1px solid #d1d5db;
            border-radius: .55rem;
            font-size: .85rem;
        }

        .campo select { background: #ffffff; }

        .campo input:focus,
        .campo select:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(18, 165, 148, 0.15);
        }

        .campo .invalido { border-color: #ef4444; }
        .campo .invalido:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
        .campo .msg-error { margin-top: .3rem; font-size: .72rem; color: #dc2626; }
        .campo .msg-error.hidden { display: none; }

        .aviso-simulado {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            border-radius: .6rem;
            padding: .75rem .9rem;
            margin-bottom: 1rem;
            font-size: .75rem;
            color: #0f766e;
            line-height: 1.4;
        }

        .btn-comprar {
            width: 100%;
            padding: .9rem 1.5rem;
            background: linear-gradient(120deg, var(--naranja), var(--teal));
            color: #ffffff;
            font-weight: 800;
            border-radius: .65rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            transition: transform .2s ease;
        }

        .btn-comprar:hover { transform: translateY(-2px); }
        .btn-comprar:disabled { cursor: not-allowed; transform: none; }

        .resumen-plan {
            background: linear-gradient(160deg, #f0fdfa, #ffffff);
            border: 1px solid #99f6e4;
            border-radius: 1rem;
            padding: 1.5rem;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar simple -->
    <header class="site-header w-full">
        <div class="max-w-5xl mx-auto flex items-center justify-between px-6 py-4">
            <a href="{{ $usuarioLogueado ? route('admin.dashboard') : url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="Logo PrismaSalud IPS" class="w-9 h-9 object-contain">
                <p class="font-bold text-gray-900">Prisma<span style="color:var(--teal);">Salud</span> IPS</p>
            </a>
            <a href="{{ $usuarioLogueado ? route('admin.planes') : route('planes.index') }}" class="text-sm font-semibold" style="color:var(--teal);">← Volver a planes</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-10">

        <div class="mb-8">
            <span class="inline-block text-xs font-extrabold uppercase tracking-[.18em]" style="color:var(--teal);">Finaliza tu compra</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2">Activa el plan {{ $plan['nombre'] }}</h1>
            @if ($usuarioLogueado)
                @if (!empty($esRenovacion))
                    <p class="text-gray-500 mt-2">Hola {{ $usuarioLogueado->nombres }}, tus datos de la IPS ya están registrados: solo necesitamos el método de pago para renovar tu plan.</p>
                @else
                    <p class="text-gray-500 mt-2">Hola {{ $usuarioLogueado->nombres }}, solo necesitamos los datos de tu IPS y el pago para activar tu plan.</p>
                @endif
            @else
                <p class="text-gray-500 mt-2">Llena tus datos y los de tu IPS para dejar tu cuenta lista en minutos.</p>
            @endif
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="text-sm font-bold text-red-800 mb-1">Revisa los siguientes campos:</p>
                <ul class="text-xs text-red-700 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1.7fr_1fr] gap-8 items-start">

            <form method="POST" action="{{ route('comprar.procesar', ['plan' => $plan['id']]) }}" class="space-y-6" id="form-comprar" novalidate data-validar>
                @csrf

                @if (empty($esRenovacion))
                <!-- Paso 1: Datos de la IPS -->
                <div class="paso">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="paso-numero">1</span>
                        <h2 class="font-extrabold text-gray-900">Datos de tu IPS</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="campo sm:col-span-2">
                            <label for="nombre_ips">Nombre de la IPS / clínica / consultorio</label>
                            <input type="text" id="nombre_ips" name="nombre_ips" value="{{ old('nombre_ips') }}" maxlength="150" data-v data-requerido="Escribe el nombre de tu IPS." data-min="3" data-max="150">
                            <p data-error-for="nombre_ips" class="msg-error {{ $errors->has('nombre_ips') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nombre_ips') }}</p>
                        </div>
                        <div class="campo">
                            <label for="nit">NIT</label>
                            <input type="text" id="nit" name="nit" value="{{ old('nit') }}" placeholder="900123456-7" maxlength="17" inputmode="numeric" data-v data-requerido="Escribe el NIT." data-patron="^\d{5,15}(-\d)?$" data-patron-msg="Solo números, con guion y dígito de verificación opcional. Ej: 900123456-7." data-solo="0-9-">
                            <p data-error-for="nit" class="msg-error {{ $errors->has('nit') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nit') }}</p>
                        </div>
                        <div class="campo">
                            <label for="tipo_entidad">Tipo de entidad</label>
                            <select id="tipo_entidad" name="tipo_entidad" data-v data-requerido="Selecciona el tipo de entidad.">
                                <option value="">Selecciona una opción</option>
                                @foreach (['IPS' => 'IPS', 'Clinica' => 'Clínica', 'EPS' => 'EPS'] as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('tipo_entidad') === $valor)>{{ $texto }}</option>
                                @endforeach
                            </select>
                            <p data-error-for="tipo_entidad" class="msg-error {{ $errors->has('tipo_entidad') ? '' : 'hidden' }}" role="alert">{{ $errors->first('tipo_entidad') }}</p>
                        </div>
                        <div class="campo sm:col-span-2">
                            <label for="representante_legal">Representante legal</label>
                            <input type="text" id="representante_legal" name="representante_legal" value="{{ old('representante_legal') }}" maxlength="150" data-v data-requerido="Escribe el nombre del representante legal." data-min="3" data-max="150" data-patron="^[\p{L}][\p{L}\s'.\-]*$" data-patron-flags="u" data-patron-msg="Solo puede tener letras y espacios.">
                            <p data-error-for="representante_legal" class="msg-error {{ $errors->has('representante_legal') ? '' : 'hidden' }}" role="alert">{{ $errors->first('representante_legal') }}</p>
                        </div>
                        <div class="campo">
                            <label for="correo_ips">Correo de la IPS</label>
                            <input type="email" id="correo_ips" name="correo_ips" value="{{ old('correo_ips') }}" placeholder="contacto@tuips.com" maxlength="150" data-v data-requerido="Escribe el correo de la IPS." data-email data-max="150">
                            <p data-error-for="correo_ips" class="msg-error {{ $errors->has('correo_ips') ? '' : 'hidden' }}" role="alert">{{ $errors->first('correo_ips') }}</p>
                        </div>
                        <div class="campo">
                            <label for="telefono_ips">Teléfono de la IPS</label>
                            <input type="text" id="telefono_ips" name="telefono_ips" value="{{ old('telefono_ips') }}" placeholder="(608) 123 4567" maxlength="20" inputmode="tel" data-v data-requerido="Escribe el teléfono de la IPS." data-patron="^[0-9 +()\-]{7,20}$" data-patron-msg="Solo números, espacios, + ( ) y guion (mínimo 7 caracteres)." data-solo="0-9 +()\-">
                            <p data-error-for="telefono_ips" class="msg-error {{ $errors->has('telefono_ips') ? '' : 'hidden' }}" role="alert">{{ $errors->first('telefono_ips') }}</p>
                        </div>
                        <div class="campo">
                            <label for="departamento">Departamento</label>
                            <select id="departamento" name="departamento" data-v data-requerido="Selecciona el departamento.">
                                <option value="">Selecciona un departamento</option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento->nombre_departamento }}" @selected(old('departamento') === $departamento->nombre_departamento)>{{ $departamento->nombre_departamento }}</option>
                                @endforeach
                            </select>
                            <p data-error-for="departamento" class="msg-error {{ $errors->has('departamento') ? '' : 'hidden' }}" role="alert">{{ $errors->first('departamento') }}</p>
                        </div>
                        <div class="campo">
                            <label for="ciudad">Ciudad</label>
                            <select id="ciudad" name="ciudad" data-v data-requerido="Selecciona la ciudad." data-old="{{ old('ciudad') }}">
                                <option value="">Selecciona un departamento primero</option>
                            </select>
                            <p data-error-for="ciudad" class="msg-error {{ $errors->has('ciudad') ? '' : 'hidden' }}" role="alert">{{ $errors->first('ciudad') }}</p>
                        </div>
                        <div class="campo sm:col-span-2">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}" placeholder="Calle 42 #5-28" maxlength="200" data-v data-requerido="Escribe la dirección." data-min="5" data-max="200">
                            <p data-error-for="direccion" class="msg-error {{ $errors->has('direccion') ? '' : 'hidden' }}" role="alert">{{ $errors->first('direccion') }}</p>
                        </div>
                    </div>
                </div>
                @else
                <!-- Datos de la IPS ya registrados (renovación): no se vuelven a pedir -->
                <div class="paso">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="paso-numero">1</span>
                        <h2 class="font-extrabold text-gray-900">Datos de tu IPS (ya registrados)</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div><p class="text-xs text-gray-400">IPS / Clínica</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->nombre_ips }}</p></div>
                        <div><p class="text-xs text-gray-400">NIT</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->nit }}</p></div>
                        <div><p class="text-xs text-gray-400">Tipo de entidad</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->tipo_entidad }}</p></div>
                        <div><p class="text-xs text-gray-400">Representante legal</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->representante_legal }}</p></div>
                        <div><p class="text-xs text-gray-400">Correo de la IPS</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->correo_ips }}</p></div>
                        <div><p class="text-xs text-gray-400">Teléfono de la IPS</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->telefono_ips }}</p></div>
                        <div><p class="text-xs text-gray-400">Ubicación</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->ciudad }}, {{ $licenciaExistente->departamento }}</p></div>
                        <div><p class="text-xs text-gray-400">Dirección</p><p class="font-semibold text-gray-800">{{ $licenciaExistente->direccion }}</p></div>
                    </div>
                    <p class="mt-4 text-xs text-gray-400">Estos datos se reutilizan de tu registro actual. Solo cambia el plan y el método de pago.</p>
                    <!-- Campos ocultos para reenviar los datos existentes (no se vuelven a validar en el front) -->
                    <input type="hidden" name="nombre_ips" value="{{ $licenciaExistente->nombre_ips }}">
                    <input type="hidden" name="nit" value="{{ $licenciaExistente->nit }}">
                    <input type="hidden" name="tipo_entidad" value="{{ $licenciaExistente->tipo_entidad }}">
                    <input type="hidden" name="representante_legal" value="{{ $licenciaExistente->representante_legal }}">
                    <input type="hidden" name="correo_ips" value="{{ $licenciaExistente->correo_ips }}">
                    <input type="hidden" name="telefono_ips" value="{{ $licenciaExistente->telefono_ips }}">
                    <input type="hidden" name="departamento" value="{{ $licenciaExistente->departamento }}">
                    <input type="hidden" name="ciudad" value="{{ $licenciaExistente->ciudad }}">
                    <input type="hidden" name="direccion" value="{{ $licenciaExistente->direccion }}">
                </div>
                @endif

                @if (!$usuarioLogueado)
                    <!-- Paso 2: Datos de tu cuenta (solo si NO hay sesion activa) -->
                    <div class="paso">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="paso-numero">2</span>
                            <h2 class="font-extrabold text-gray-900">Tu cuenta de Administrador</h2>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="campo">
                                <label for="cedula">Cédula</label>
                                <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" maxlength="10" inputmode="numeric" data-v data-requerido="Escribe tu cédula." data-patron="^\d{6,10}$" data-patron-msg="La cédula debe tener entre 6 y 10 dígitos." data-solo="0-9">
                                <p data-error-for="cedula" class="msg-error {{ $errors->has('cedula') ? '' : 'hidden' }}" role="alert">{{ $errors->first('cedula') }}</p>
                            </div>
                            <div class="campo">
                                <label for="telefono">Teléfono (opcional)</label>
                                <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" maxlength="20" inputmode="tel" data-v data-patron="^[0-9 +()\-]{7,20}$" data-patron-msg="Solo números, espacios, + ( ) y guion (mínimo 7 caracteres)." data-solo="0-9 +()\-">
                                <p data-error-for="telefono" class="msg-error {{ $errors->has('telefono') ? '' : 'hidden' }}" role="alert">{{ $errors->first('telefono') }}</p>
                            </div>
                            <div class="campo">
                                <label for="nombres">Nombres</label>
                                <input type="text" id="nombres" name="nombres" value="{{ old('nombres') }}" maxlength="100" data-v data-requerido="Escribe tus nombres." data-max="100" data-patron="^[\p{L}][\p{L}\s'.\-]*$" data-patron-flags="u" data-patron-msg="Solo puede tener letras.">
                                <p data-error-for="nombres" class="msg-error {{ $errors->has('nombres') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nombres') }}</p>
                            </div>
                            <div class="campo">
                                <label for="apellidos">Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" maxlength="100" data-v data-requerido="Escribe tus apellidos." data-max="100" data-patron="^[\p{L}][\p{L}\s'.\-]*$" data-patron-flags="u" data-patron-msg="Solo puede tener letras.">
                                <p data-error-for="apellidos" class="msg-error {{ $errors->has('apellidos') ? '' : 'hidden' }}" role="alert">{{ $errors->first('apellidos') }}</p>
                            </div>
                            <div class="campo sm:col-span-2">
                                <label for="correo">Correo</label>
                                <input type="email" id="correo" name="correo" value="{{ old('correo') }}" maxlength="150" data-v data-requerido="Escribe tu correo." data-email data-max="150">
                                <p data-error-for="correo" class="msg-error {{ $errors->has('correo') ? '' : 'hidden' }}" role="alert">{{ $errors->first('correo') }}</p>
                            </div>
                            <div class="campo">
                                <label for="contrasena">Contraseña</label>
                                <input type="password" id="contrasena" name="contrasena" autocomplete="new-password" data-v data-requerido="Escribe tu contraseña." data-min="6" data-max="100" data-patron="^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,100}$" data-patron-msg="La contraseña debe incluir al menos una letra mayúscula y un carácter especial (por ejemplo @, #, !)." data-revalida="#contrasena_confirmation">
                                <p data-error-for="contrasena" class="msg-error {{ $errors->has('contrasena') ? '' : 'hidden' }}" role="alert">{{ $errors->first('contrasena') }}</p>
                            </div>
                            <div class="campo">
                                <label for="contrasena_confirmation">Confirmar contraseña</label>
                                <input type="password" id="contrasena_confirmation" name="contrasena_confirmation" autocomplete="new-password" data-v data-requerido="Confirma tu contraseña." data-min="6" data-max="100" data-igual="#contrasena" data-igual-msg="Las dos contraseñas no coinciden.">
                                <p data-error-for="contrasena_confirmation" class="msg-error {{ $errors->has('contrasena_confirmation') ? '' : 'hidden' }}" role="alert">{{ $errors->first('contrasena_confirmation') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Paso de pago (simulado) -->
                <div class="paso">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="paso-numero">{{ $usuarioLogueado ? 2 : 3 }}</span>
                        <h2 class="font-extrabold text-gray-900">Datos de pago</h2>
                    </div>

                    <div class="aviso-simulado">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Este pago es simulado. No procesamos tu tarjeta ni la guardamos completa: solo dejamos los últimos 4 dígitos como referencia interna.</span>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="campo sm:col-span-2">
                            <label for="metodo_pago">Método de pago</label>
                            <select id="metodo_pago" name="metodo_pago" data-v data-requerido="Selecciona un método de pago.">
                                <option value="">Selecciona un método de pago</option>
                                @foreach ([
                                    'tarjeta_credito' => 'Tarjeta de crédito',
                                    'tarjeta_debito'  => 'Tarjeta débito',
                                    'pse'             => 'PSE (cuenta bancaria)',
                                    'nequi'           => 'Nequi',
                                    'daviplata'       => 'Daviplata',
                                    'transferencia'   => 'Transferencia bancaria',
                                    'efecty'          => 'Efecty (pago en efectivo)',
                                ] as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('metodo_pago', 'tarjeta_credito') === $valor)>{{ $texto }}</option>
                                @endforeach
                            </select>
                            <p data-error-for="metodo_pago" class="msg-error {{ $errors->has('metodo_pago') ? '' : 'hidden' }}" role="alert">{{ $errors->first('metodo_pago') }}</p>
                        </div>
                    </div>

                    <!-- Bloque tarjeta (solo para métodos de tarjeta) -->
                    <div id="bloque-tarjeta" class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div class="campo sm:col-span-2">
                            <label for="nombre_tarjeta">Nombre en la tarjeta</label>
                            <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" value="{{ old('nombre_tarjeta') }}" maxlength="100" autocomplete="cc-name" data-v data-requerido="Escribe el nombre que aparece en la tarjeta." data-min="3" data-max="100" data-patron="^[\p{L}][\p{L}\s'.\-]*$" data-patron-flags="u" data-patron-msg="Solo puede tener letras.">
                            <p data-error-for="nombre_tarjeta" class="msg-error {{ $errors->has('nombre_tarjeta') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nombre_tarjeta') }}</p>
                        </div>
                        <div class="campo sm:col-span-2">
                            <label for="numero_tarjeta">Número de tarjeta</label>
                            <input type="text" id="numero_tarjeta" name="numero_tarjeta" value="{{ old('numero_tarjeta') }}" placeholder="4111 1111 1111 1111" maxlength="19" inputmode="numeric" autocomplete="cc-number" data-v data-requerido="Escribe el número de la tarjeta." data-tarjeta>
                            <p data-error-for="numero_tarjeta" class="msg-error {{ $errors->has('numero_tarjeta') ? '' : 'hidden' }}" role="alert">{{ $errors->first('numero_tarjeta') }}</p>
                        </div>
                        <div class="campo">
                            <label for="vencimiento_tarjeta">Vencimiento (MM/AA)</label>
                            <input type="text" id="vencimiento_tarjeta" name="vencimiento_tarjeta" value="{{ old('vencimiento_tarjeta') }}" placeholder="12/28" maxlength="5" inputmode="numeric" autocomplete="cc-exp" data-v data-requerido="Escribe el vencimiento." data-vence>
                            <p data-error-for="vencimiento_tarjeta" class="msg-error {{ $errors->has('vencimiento_tarjeta') ? '' : 'hidden' }}" role="alert">{{ $errors->first('vencimiento_tarjeta') }}</p>
                        </div>
                        <div class="campo">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" inputmode="numeric" autocomplete="cc-csc" data-v data-requerido="Escribe el CVV." data-patron="^\d{3,4}$" data-patron-msg="El CVV tiene 3 o 4 dígitos." data-solo="0-9">
                            <p data-error-for="cvv" class="msg-error {{ $errors->has('cvv') ? '' : 'hidden' }}" role="alert">{{ $errors->first('cvv') }}</p>
                        </div>
                    </div>

                    <!-- Bloque de referencia (métodos distintos a tarjeta) -->
                    <div id="bloque-referencia" class="grid sm:grid-cols-2 gap-4 mt-4 hidden">
                        <div class="campo sm:col-span-2">
                            <label for="referencia">Referencia / código de confirmación</label>
                            <input type="text" id="referencia" name="referencia" value="{{ old('referencia') }}" maxlength="100" data-v data-requerido="Escribe la referencia o código de confirmación del método elegido." data-min="3" data-max="100">
                            <p class="text-xs text-gray-400 mt-1">Ejemplo: número de cuenta, celular asociado o código de aprobación.</p>
                            <p data-error-for="referencia" class="msg-error {{ $errors->has('referencia') ? '' : 'hidden' }}" role="alert">{{ $errors->first('referencia') }}</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-comprar" id="btn-comprar">
                    @if (!empty($esRenovacion))
                        Confirmar pago y renovar mi plan
                    @elseif ($usuarioLogueado)
                        Confirmar pago y activar mi cuenta
                    @else
                        Confirmar pago y activar mi cuenta
                    @endif
                </button>
            </form>

            <!-- Resumen del plan -->
            <aside class="resumen-plan lg:sticky lg:top-8">
                <p class="text-xs font-extrabold uppercase tracking-wider" style="color:var(--teal);">Resumen</p>
                <h3 class="text-xl font-extrabold text-gray-900 mt-1 mb-3">{{ $plan['nombre'] }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $plan['descripcion'] }}</p>

                <div class="border-t border-teal-100 pt-4 mb-4">
                    @if ($plan['precio_total'] === 0)
                        <p class="text-2xl font-extrabold" style="color:var(--teal);">Gratis</p>
                    @else
                        <p class="text-2xl font-extrabold text-gray-900">${{ number_format($plan['precio_total'], 0, ',', '.') }}</p>
                    @endif
                    <p class="text-xs text-gray-400">{{ $plan['periodo'] }}</p>
                </div>

                <ul class="space-y-2">
                    @foreach (array_slice($plan['beneficios'], 0, 5) as $beneficio)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--teal);"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg>
                            <span>{{ $beneficio }}</span>
                        </li>
                    @endforeach
                </ul>
            </aside>
        </div>
    </main>

    <script>
    // Validación del navegador (JavaScript). El servidor vuelve a validar todo:
    // esto solo da respuesta rápida y mensajes claros debajo de cada campo.
    (function () {
        var form = document.getElementById('form-comprar');
        if (!form) return;

        var campos = Array.prototype.slice.call(form.querySelectorAll('[data-v]'));
        var tocados = {};

        // Selects dinámicos de Departamento -> Ciudad (datos de la base de datos).
        var deptoSelect = document.getElementById('departamento');
        var ciudadSelect = document.getElementById('ciudad');
        var ciudadesMap = @json($ciudadesPorDepartamento);
        var oldCiudad = @json(old('ciudad'));
        function llenarCiudades(init) {
            if (!deptoSelect || !ciudadSelect) return;
            var depto = deptoSelect.value;
            var seleccionada = init ? (oldCiudad || '') : (ciudadSelect.value || '');
            ciudadSelect.innerHTML = '';
            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = (depto && ciudadesMap[depto] && ciudadesMap[depto].length)
                ? 'Selecciona la ciudad' : 'Selecciona un departamento primero';
            ciudadSelect.appendChild(placeholder);
            if (depto && ciudadesMap[depto]) {
                ciudadesMap[depto].forEach(function (c) {
                    var opt = document.createElement('option');
                    opt.value = c;
                    opt.textContent = c;
                    if (init && c === seleccionada) opt.selected = true;
                    ciudadSelect.appendChild(opt);
                });
                if (!init) {
                    var idx = ciudadesMap[depto].indexOf(seleccionada);
                    if (idx !== -1) ciudadSelect.value = seleccionada;
                }
            }
        }
        if (deptoSelect) {
            deptoSelect.addEventListener('change', function () {
                ciudadSelect.value = '';
                llenarCiudades(false);
                if (tocados['ciudad']) validar(ciudadSelect);
            });
        }
        llenarCiudades(true);

        // Muestra/oculta los campos de tarjeta vs. referencia según el método de pago elegido.
        var metodoPago = form.querySelector('[name="metodo_pago"]');
        function alternarMetodoPago() {
            if (!metodoPago) return;
            var v = metodoPago.value;
            var esTarjeta = (v === 'tarjeta_credito' || v === 'tarjeta_debito');
            var bloqueTarjeta = document.getElementById('bloque-tarjeta');
            var bloqueRef = document.getElementById('bloque-referencia');
            if (bloqueTarjeta) {
                bloqueTarjeta.classList.toggle('hidden', !esTarjeta);
                bloqueTarjeta.querySelectorAll('input').forEach(function (i) { i.disabled = !esTarjeta; });
            }
            if (bloqueRef) {
                bloqueRef.classList.toggle('hidden', esTarjeta);
                bloqueRef.querySelectorAll('input').forEach(function (i) { i.disabled = esTarjeta; });
            }
        }
        if (metodoPago) {
            metodoPago.addEventListener('change', alternarMetodoPago);
            alternarMetodoPago();
        }

        function buscar(selector) { return form.querySelector(selector); }
        function soloDigitos(t) { return t.replace(/\D/g, ''); }

        // Devuelve el mensaje de error del campo, o '' si está bien.
        function errorDe(c) {
            var crudo = c.value;
            var t = c.type === 'password' ? crudo : crudo.trim();

            // Los campos deshabilitados (del bloque de pago no activo) no se validan.
            if (c.disabled) return '';

            if (t === '') {
                return c.getAttribute('data-requerido') || '';
            }

            var min = c.getAttribute('data-min');
            if (min && t.length < parseInt(min, 10)) return 'Escribe mínimo ' + min + ' caracteres.';
            var max = c.getAttribute('data-max');
            if (max && t.length > parseInt(max, 10)) return 'No puede tener más de ' + max + ' caracteres.';

            if (c.hasAttribute('data-email') && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(t)) {
                return 'Escribe un correo válido, por ejemplo nombre@dominio.com.';
            }

            // Número de tarjeta: de 13 a 16 dígitos (los espacios no cuentan)
            if (c.hasAttribute('data-tarjeta')) {
                var n = soloDigitos(t).length;
                if (n < 13 || n > 16) return 'El número debe tener entre 13 y 16 dígitos.';
                return '';
            }

            // Vencimiento MM/AA: mes válido y que no esté vencida
            if (c.hasAttribute('data-vence')) {
                var m = /^(\d{2})\/(\d{2})$/.exec(t);
                if (!m) return 'Escribe el vencimiento como MM/AA, por ejemplo 12/28.';
                var mes = parseInt(m[1], 10), anio = 2000 + parseInt(m[2], 10);
                if (mes < 1 || mes > 12) return 'El mes debe estar entre 01 y 12.';
                var hoy = new Date();
                if (anio < hoy.getFullYear() || (anio === hoy.getFullYear() && mes < hoy.getMonth() + 1)) {
                    return 'La tarjeta está vencida.';
                }
                if (anio > hoy.getFullYear() + 20) return 'El año del vencimiento no es válido.';
                return '';
            }

            var patron = c.getAttribute('data-patron');
            if (patron && !new RegExp(patron, c.getAttribute('data-patron-flags') || '').test(t)) {
                return c.getAttribute('data-patron-msg') || 'El formato no es válido.';
            }

            var igual = c.getAttribute('data-igual');
            if (igual) {
                var otro = buscar(igual);
                if (otro && otro.value !== crudo) return c.getAttribute('data-igual-msg') || 'Los dos valores no coinciden.';
            }

            return '';
        }

        function pintar(c, mensaje) {
            var caja = form.querySelector('[data-error-for="' + c.name + '"]');
            if (caja) {
                caja.textContent = mensaje;
                caja.classList.toggle('hidden', mensaje === '');
            }
            c.classList.toggle('invalido', mensaje !== '');
            c.setAttribute('aria-invalid', mensaje !== '' ? 'true' : 'false');
        }

        function validar(c) {
            var m = errorDe(c);
            pintar(c, m);
            return m === '';
        }

        // Formato mientras escribe: tarjeta con espacios cada 4, vencimiento con "/"
        function formatear(c) {
            if (c.hasAttribute('data-tarjeta')) {
                var d = soloDigitos(c.value).slice(0, 16);
                c.value = d.replace(/(\d{4})(?=\d)/g, '$1 ');
            } else if (c.hasAttribute('data-vence')) {
                var v = soloDigitos(c.value).slice(0, 4);
                c.value = v.length > 2 ? v.slice(0, 2) + '/' + v.slice(2) : v;
            } else if (c.getAttribute('data-solo')) {
                var permitido = new RegExp('[^' + c.getAttribute('data-solo') + ']', 'g');
                c.value = c.value.replace(permitido, '');
            }
        }

        campos.forEach(function (c) {
            function alEscribir() {
                formatear(c);
                if (tocados[c.name]) validar(c);
                var rev = c.getAttribute('data-revalida');
                if (rev) {
                    var o = buscar(rev);
                    if (o && tocados[o.name]) validar(o);
                }
            }
            c.addEventListener('input', alEscribir);
            c.addEventListener('change', alEscribir);
            c.addEventListener('blur', function () { tocados[c.name] = true; validar(c); });
        });

        form.addEventListener('submit', function (e) {
            var primero = null;
            campos.forEach(function (c) {
                tocados[c.name] = true;
                if (!validar(c) && !primero) primero = c;
            });
            if (primero) {
                e.preventDefault();
                primero.focus();
                primero.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            // Evita que un doble clic envíe la compra dos veces y cree dos licencias.
            var boton = document.getElementById('btn-comprar');
            boton.disabled = true;
            boton.textContent = 'Procesando pago...';
        });

        // Si el servidor devolvió errores, se enfoca el primer campo con error
        var conError = form.querySelector('[data-error-for]:not(.hidden)');
        if (conError) {
            var campo = form.querySelector('[name="' + conError.getAttribute('data-error-for') + '"]');
            if (campo) { campo.classList.add('invalido'); campo.focus(); }
        }
    })();
    </script>

    <script src="{{ asset('js/validacion-live.js') }}"></script>
</body>
</html>