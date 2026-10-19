{{--
    =====================================================================
    VISTA DE CORREO: emails/codigo-primer-ingreso.blade.php
    Correo que recibe el cotizante con su código de 6 dígitos para
    completar el primer ingreso y crear su contraseña.
    Las variables $codigo y $nombreUsuario vienen del PrimerIngresoController.
    =====================================================================
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 30px;">

    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 10px; padding: 30px; text-align: center;">

        <p style="color: #12A594; font-weight: bold; font-size: 18px; margin-bottom: 4px;">
            PrismaSalud IPS
        </p>

        <h2 style="color: #111827; margin-top: 20px;">Hola, {{ $nombreUsuario }}</h2>

        <p style="color: #4b5563; font-size: 14px;">
            Estás creando tu contraseña por primera vez en PrismaSalud.
            Usa el siguiente código para verificar tu correo (válido por 10 minutos):
        </p>

        <div style="background-color: #f0fdfa; border: 1px dashed #12A594; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <span style="font-size: 28px; font-weight: bold; letter-spacing: 6px; color: #12A594;">
                {{ $codigo }}
            </span>
        </div>

        <p style="color: #9ca3af; font-size: 12px;">
            Si tú no solicitaste este código, ignora este correo. Nadie podrá
            crear tu contraseña sin este código.
        </p>
    </div>

</body>
</html>
