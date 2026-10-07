{{--
    =====================================================================
    VISTA DE CORREO: emails/codigo-login-superadmin.blade.php
    Correo que recibe el superadmin con su código de verificación
    para iniciar sesión en el panel. Las variables $codigo y
    $nombreAdmin vienen del SuperAdminLoginController.
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
            PrismaSalud Panel de Superadministrador
        </p>

        <h2 style="color: #111827; margin-top: 20px;">Hola, {{ $nombreAdmin }}</h2>

        <p style="color: #4b5563; font-size: 14px;">
            Estás intentando ingresar al panel de Superadministrador de PrismaSalud.
            Usa el siguiente código para verificar que eres tú (válido por 10 minutos):
        </p>

        <div style="background-color: #f0fdfa; border: 1px dashed #12A594; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <span style="font-size: 28px; font-weight: bold; letter-spacing: 6px; color: #12A594;">
                {{ $codigo }}
            </span>
        </div>

        <p style="color: #9ca3af; font-size: 12px;">
            Si tú no intentaste ingresar, ignora este correo: nadie podrá
            entrar a tu cuenta sin este código.
        </p>
    </div>

</body>
</html>
