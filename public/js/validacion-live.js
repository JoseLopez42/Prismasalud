// Validación en vivo para todos los formularios.
// Usa los atributos HTML (required, type, minlength, maxlength, min, max, pattern)
// para mostrar un mensaje debajo del campo mientras se escribe.
(function () {
    function mensajeError(campo) {
        var valor = campo.type === 'password' ? campo.value : campo.value.trim();

        if (valor === '' && campo.hasAttribute('required')) {
            return 'Este campo es obligatorio.';
        }

        if (valor === '') return '';

        if (campo.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valor)) {
            return 'Escribe un correo valido, por ejemplo nombre@dominio.com.';
        }

        if (campo.hasAttribute('minlength') && valor.length < parseInt(campo.getAttribute('minlength'), 10)) {
            return 'Escribe minimo ' + campo.getAttribute('minlength') + ' caracteres.';
        }

        if (campo.hasAttribute('maxlength') && valor.length > parseInt(campo.getAttribute('maxlength'), 10)) {
            return 'No puede tener mas de ' + campo.getAttribute('maxlength') + ' caracteres.';
        }

        if (campo.type === 'number') {
            var num = Number(valor);
            if (campo.hasAttribute('min') && num < parseFloat(campo.getAttribute('min'))) {
                return 'El valor minimo es ' + campo.getAttribute('min') + '.';
            }
            if (campo.hasAttribute('max') && num > parseFloat(campo.getAttribute('max'))) {
                return 'El valor maximo es ' + campo.getAttribute('max') + '.';
            }
        }

        if (campo.hasAttribute('pattern')) {
            try {
                var patron = new RegExp(campo.getAttribute('pattern'), campo.getAttribute('pattern-flags') || '');
                if (!patron.test(valor)) {
                    return campo.getAttribute('data-patron-msg') || 'El formato no es valido.';
                }
            } catch (e) {}
        }

        if (campo.type === 'password' && valor.length > 0 && valor.length < 4) {
            return 'La contrasena es muy corta.';
        }

        return '';
    }

    function crearError(campo) {
        var span = document.createElement('span');
        span.className = 'validacion-live-error text-xs text-red-600 mt-1 block';
        span.style.display = 'none';
        campo.insertAdjacentElement('afterend', span);
        return span;
    }

    function validar(campo) {
        var mensaje = mensajeError(campo);
        var error = campo._validacionLiveError;
        if (error) {
            error.textContent = mensaje;
            error.style.display = mensaje ? 'block' : 'none';
        }
        campo.classList.toggle('border-red-500', !!mensaje);
        return mensaje === '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function (form) {
            var campos = Array.prototype.slice.call(form.querySelectorAll('input, select, textarea'));
            campos.forEach(function (campo) {
                campo._validacionLiveError = crearError(campo);
                campo.addEventListener('input', function () { validar(campo); });
                campo.addEventListener('blur', function () { validar(campo); });
            });

            form.addEventListener('submit', function (e) {
                var primero = null;
                campos.forEach(function (campo) {
                    if (!validar(campo) && !primero) primero = campo;
                });
                if (primero) {
                    e.preventDefault();
                    primero.focus();
                }
            });
        });
    });
})();