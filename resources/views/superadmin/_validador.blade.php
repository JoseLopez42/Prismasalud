{{-- Validación del navegador (JavaScript) para los formularios del panel.
     El servidor vuelve a validar lo mismo: esto solo da respuesta rápida.
     Se incluye con @include('superadmin._validador') y usa atributos data-* en cada campo. --}}
@push('scripts')
<script>
(function () {
    var form = document.querySelector('form[data-validar]');
    if (!form) return;

    var campos = Array.prototype.slice.call(form.querySelectorAll('[data-v]'));
    var tocados = {};

    function buscar(selector) { return form.querySelector(selector); }

    // Devuelve el mensaje de error del campo, o '' si está bien.
    function errorDe(c) {
        var crudo = c.value;
        var t = c.type === 'password' ? crudo : crudo.trim();
        var requerido = c.getAttribute('data-requerido');

        if (t === '') {
            if (requerido) return requerido;
            var ig = c.getAttribute('data-igual');
            if (ig) {
                var otro = buscar(ig);
                if (otro && otro.value !== '') return c.getAttribute('data-igual-msg') || 'Repite la contraseña.';
            }
            return '';
        }

        var min = c.getAttribute('data-min');
        if (min && t.length < parseInt(min, 10)) return 'Escribe mínimo ' + min + ' caracteres.';
        var max = c.getAttribute('data-max');
        if (max && t.length > parseInt(max, 10)) return 'No puede tener más de ' + max + ' caracteres.';

        if (c.hasAttribute('data-email') && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(t)) {
            return 'Escribe un correo válido, por ejemplo nombre@dominio.com.';
        }

        var patron = c.getAttribute('data-patron');
        if (patron && !new RegExp(patron, c.getAttribute('data-patron-flags') || '').test(t)) {
            return c.getAttribute('data-patron-msg') || 'El formato no es válido.';
        }

        if (c.hasAttribute('data-clave')) {
            if (t.length < 8) return 'La contraseña debe tener mínimo 8 caracteres.';
            if (!(/[A-Z]/.test(t) && /[a-z]/.test(t) && /[0-9]/.test(t) && /[^A-Za-z0-9]/.test(t))) {
                return 'Falta cumplir alguno de los requisitos de la lista.';
            }
        }

        var igual = c.getAttribute('data-igual');
        if (igual) {
            var otro2 = buscar(igual);
            if (otro2 && otro2.value !== crudo) return c.getAttribute('data-igual-msg') || 'Los dos valores no coinciden.';
        }

        var fechaMin = c.getAttribute('data-fecha-min');
        if (fechaMin) {
            var base = buscar(fechaMin);
            if (base && base.value && t < base.value) return c.getAttribute('data-fecha-msg') || 'La fecha no puede ser anterior.';
        }

        return '';
    }

    function pintar(c, mensaje) {
        var caja = form.querySelector('[data-error-for="' + c.name + '"]');
        if (caja) {
            caja.textContent = mensaje;
            caja.classList.toggle('hidden', mensaje === '');
        }
        c.classList.toggle('border-red-500', mensaje !== '');
        c.classList.toggle('border-gray-300', mensaje === '');
        c.setAttribute('aria-invalid', mensaje !== '' ? 'true' : 'false');
    }

    function validar(c) {
        var m = errorDe(c);
        pintar(c, m);
        return m === '';
    }

    // Lista de requisitos de la contraseña (se pone verde lo que ya cumple)
    function pintarRequisitos(c) {
        var items = form.querySelectorAll('#requisitos li');
        if (!items.length) return;
        var v = c.value;
        var r = { largo: v.length >= 8, mayuscula: /[A-Z]/.test(v), minuscula: /[a-z]/.test(v), numero: /[0-9]/.test(v), especial: /[^A-Za-z0-9]/.test(v) };
        items.forEach(function (li) {
            var ok = r[li.getAttribute('data-req')];
            li.classList.toggle('text-green-600', ok);
            li.classList.toggle('font-semibold', ok);
            li.classList.toggle('text-gray-400', !ok);
        });
    }

    campos.forEach(function (c) {
        function alEscribir() {
            if (c.hasAttribute('data-clave')) pintarRequisitos(c);
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
            return;
        }
        var btn = form.querySelector('[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }
    });

    // Si el servidor devolvió errores, se enfoca el primer campo con error
    var conError = form.querySelector('[data-error-for]:not(.hidden)');
    if (conError) {
        var campo = form.querySelector('[name="' + conError.getAttribute('data-error-for') + '"]');
        if (campo) campo.focus();
    }
})();
</script>
@endpush