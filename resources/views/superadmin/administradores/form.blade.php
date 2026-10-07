@extends('superadmin.layout')
@php $editando = $admin !== null; @endphp
@section('titulo', $editando ? 'Editar administrador' : 'Nuevo administrador')

@php
    $input = 'w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $borde = fn ($campo) => $errors->has($campo) ? 'border-red-500' : 'border-gray-300';
    $valor = fn ($campo, $defecto = '') => old($campo, $editando ? ($admin->{$campo} ?? $defecto) : $defecto);
    $patronNombre = "^[\\p{L}][\\p{L}\\s'.\\-]*$";
@endphp

@section('contenido')
    <a href="{{ route('superadmin.administradores.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-900">← Volver a Administradores</a>
    <h1 class="text-2xl font-extrabold text-gray-900 mt-2">{{ $editando ? 'Editar administrador' : 'Nuevo administrador' }}</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">
        {{ $editando ? 'Corrige sus datos o cámbiale la contraseña. Si dejas la contraseña vacía, se queda la que tiene.' : 'Al guardar, esta persona podrá entrar por el login normal con su correo y contraseña.' }}
    </p>

    <form method="POST" novalidate data-validar
          action="{{ $editando ? route('superadmin.administradores.update', $admin->cedula) : route('superadmin.administradores.store') }}"
          class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-8">
        @csrf
        @if ($editando) @method('PUT') @endif

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Datos personales</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="cedula" class="block text-xs font-bold text-gray-700 mb-1">Cédula *</label>
                    @if ($editando)
                        <input id="cedula" type="text" value="{{ $admin->cedula }}" disabled
                               class="{{ $input }} border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed">
                        <p class="text-[11px] text-gray-400 mt-1">La cédula no se puede cambiar.</p>
                    @else
                        <input id="cedula" name="cedula" type="text" inputmode="numeric" maxlength="10" value="{{ old('cedula') }}" autofocus
                               data-v data-requerido="Escribe la cédula." data-patron="^[0-9]{5,10}$" data-patron-msg="La cédula solo lleva números (entre 5 y 10)."
                               class="{{ $input }} {{ $borde('cedula') }}">
                        <p data-error-for="cedula" class="text-xs text-red-600 mt-1 {{ $errors->has('cedula') ? '' : 'hidden' }}" role="alert">{{ $errors->first('cedula') }}</p>
                    @endif
                </div>

                <div>
                    <label for="telefono" class="block text-xs font-bold text-gray-700 mb-1">Teléfono</label>
                    <input id="telefono" name="telefono" type="text" inputmode="tel" value="{{ $valor('telefono') }}"
                           data-v data-patron="^[0-9+\s-]{7,20}$" data-patron-msg="Solo números (7 a 20); puede tener + o espacios."
                           class="{{ $input }} {{ $borde('telefono') }}">
                    <p data-error-for="telefono" class="text-xs text-red-600 mt-1 {{ $errors->has('telefono') ? '' : 'hidden' }}" role="alert">{{ $errors->first('telefono') }}</p>
                </div>

                <div>
                    <label for="nombres" class="block text-xs font-bold text-gray-700 mb-1">Nombres *</label>
                    <input id="nombres" name="nombres" type="text" value="{{ $valor('nombres') }}" @if ($editando) autofocus @endif
                           data-v data-requerido="Escribe los nombres." data-max="100"
                           data-patron="{{ $patronNombre }}" data-patron-flags="u" data-patron-msg="Los nombres solo pueden tener letras."
                           class="{{ $input }} {{ $borde('nombres') }}">
                    <p data-error-for="nombres" class="text-xs text-red-600 mt-1 {{ $errors->has('nombres') ? '' : 'hidden' }}" role="alert">{{ $errors->first('nombres') }}</p>
                </div>

                <div>
                    <label for="apellidos" class="block text-xs font-bold text-gray-700 mb-1">Apellidos *</label>
                    <input id="apellidos" name="apellidos" type="text" value="{{ $valor('apellidos') }}"
                           data-v data-requerido="Escribe los apellidos." data-max="100"
                           data-patron="{{ $patronNombre }}" data-patron-flags="u" data-patron-msg="Los apellidos solo pueden tener letras."
                           class="{{ $input }} {{ $borde('apellidos') }}">
                    <p data-error-for="apellidos" class="text-xs text-red-600 mt-1 {{ $errors->has('apellidos') ? '' : 'hidden' }}" role="alert">{{ $errors->first('apellidos') }}</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-sm font-extrabold text-prisma-azul uppercase tracking-wide mb-4">Acceso y empresa</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="correo" class="block text-xs font-bold text-gray-700 mb-1">Correo *</label>
                    <input id="correo" name="correo" type="email" value="{{ $valor('correo') }}" autocomplete="off"
                           data-v data-requerido="Escribe el correo." data-email data-max="150"
                           class="{{ $input }} {{ $borde('correo') }}">
                    <p data-error-for="correo" class="text-xs text-red-600 mt-1 {{ $errors->has('correo') ? '' : 'hidden' }}" role="alert">{{ $errors->first('correo') }}</p>
                </div>

                <div>
                    <label for="licencia_id" class="block text-xs font-bold text-gray-700 mb-1">Empresa *</label>
                    <select id="licencia_id" name="licencia_id" data-v data-requerido="Elige la empresa del administrador."
                            class="{{ $input }} {{ $borde('licencia_id') }} bg-white">
                        <option value="">Elige una empresa…</option>
                        @foreach ($empresas as $e)
                            <option value="{{ $e->id }}" @selected((string) old('licencia_id', $editando ? $admin->licencia_id : $empresaPreseleccionada) === (string) $e->id)>{{ $e->nombre_ips }}</option>
                        @endforeach
                    </select>
                    <p data-error-for="licencia_id" class="text-xs text-red-600 mt-1 {{ $errors->has('licencia_id') ? '' : 'hidden' }}" role="alert">{{ $errors->first('licencia_id') }}</p>
                    @if ($empresas->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">Todavía no hay empresas. <a href="{{ route('superadmin.empresas.create') }}" class="font-bold underline">Crea una primero</a>.</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-1">{{ $editando ? 'Contraseña nueva (opcional)' : 'Contraseña *' }}</label>
                    <input id="password" name="password" type="password" autocomplete="new-password"
                           data-v @unless ($editando) data-requerido="Escribe una contraseña." @endunless data-clave data-max="100" data-revalida="#password_confirmation"
                           class="{{ $input }} {{ $borde('password') }}">
                    <p data-error-for="password" class="text-xs text-red-600 mt-1 {{ $errors->has('password') ? '' : 'hidden' }}" role="alert">{{ $errors->first('password') }}</p>
                    <ul class="mt-2 space-y-0.5 text-xs text-gray-400" id="requisitos">
                        <li data-req="largo">• Mínimo 8 caracteres</li>
                        <li data-req="mayuscula">• Una letra mayúscula</li>
                        <li data-req="minuscula">• Una letra minúscula</li>
                        <li data-req="numero">• Un número</li>
                        <li data-req="especial">• Un carácter especial (* # ! $ ...)</li>
                    </ul>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1">Repite la contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                           data-v @unless ($editando) data-requerido="Repite la contraseña." @endunless data-igual="#password" data-igual-msg="Las dos contraseñas no coinciden."
                           class="{{ $input }} {{ $borde('password_confirmation') }}">
                    <p data-error-for="password_confirmation" class="text-xs text-red-600 mt-1 {{ $errors->has('password_confirmation') ? '' : 'hidden' }}" role="alert">{{ $errors->first('password_confirmation') }}</p>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 transition disabled:opacity-60">
                {{ $editando ? 'Guardar cambios' : 'Crear administrador' }}
            </button>
            <a href="{{ route('superadmin.administradores.index') }}" class="rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold px-5 py-2.5 transition">Cancelar</a>
        </div>
    </form>

    @include('superadmin._validador')
@endsection