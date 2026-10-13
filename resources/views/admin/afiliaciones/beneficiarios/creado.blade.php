@extends('admin.layout')

@section('titulo', 'Cuenta creada')
@section('encabezado', 'Afiliaciones · Cuenta creada')

@section('contenido')

    @php $esBeneficiario = ($credenciales['tipo_persona'] ?? '') === 'beneficiario'; @endphp

    <div class="max-w-2xl">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-teal-500 px-6 py-6 text-white">
                <p class="font-bold text-lg leading-tight">
                    {{ $esBeneficiario ? 'Beneficiario registrado' : 'Cotizante registrado' }}
                </p>
                <p class="text-xs text-white/70">{{ session('exito') ?? 'La cuenta de acceso ya está lista.' }}</p>
            </div>

            <div class="p-6 space-y-5">

                <div class="border border-gray-200 rounded-xl divide-y divide-gray-100">
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <span class="text-gray-500">Nombre</span>
                        <span class="font-semibold text-gray-900 text-right">{{ $credenciales['nombres'] }}</span>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <span class="text-gray-500">Tipo</span>
                        <span class="font-semibold text-gray-900 text-right">{{ $esBeneficiario ? 'Beneficiario' : 'Cotizante' }}</span>
                    </div>
                    @if ($esBeneficiario)
                        <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                            <span class="text-gray-500">Familiar de</span>
                            <span class="font-semibold text-gray-900 text-right">
                                {{ $credenciales['cotizante'] ?? '—' }}
                                @if (!empty($credenciales['parentesco']))
                                    <span class="font-normal text-gray-500">({{ $credenciales['parentesco'] }})</span>
                                @endif
                            </span>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <span class="text-gray-500">EPS</span>
                        <span class="font-semibold text-gray-900 text-right">{{ $credenciales['eps'] ?? 'Sin EPS asignada' }}</span>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm bg-teal-50/60">
                        <span class="text-gray-500">Su cuenta (usuario)</span>
                        <span class="font-bold text-teal-700 text-lg tracking-wide">{{ $credenciales['cc_afiliado'] }}</span>
                    </div>
                </div>

                <div class="bg-teal-50 border border-teal-100 text-teal-800 text-sm rounded-xl px-4 py-4">
                    <p class="font-semibold">Primer ingreso</p>
                    <p class="mt-1">Inicia sesión con tu cédula y crea tu contraseña para ingresar.</p>
                </div>

                <div class="flex gap-3 pt-1">
                    <a href="{{ route('beneficiarios.index') }}"
                       class="flex-1 inline-flex items-center justify-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition">
                        Volver al panel
                    </a>
                    <a href="{{ route('beneficiarios.create') }}"
                       class="flex-1 inline-flex items-center justify-center bg-green-600 text-white font-semibold py-2.5 rounded-lg hover:bg-green-700 hover:shadow-md transition">
                        Registrar otra persona
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection