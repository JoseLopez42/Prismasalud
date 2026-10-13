<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 30px;">
    <div style="max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 10px; padding: 30px;">

        <p style="color: #12A594; font-weight: bold; font-size: 18px; margin-bottom: 4px; text-align: center;">
            PrismaSalud IPS
        </p>

        <h2 style="color: #111827; margin-top: 20px;">
            Hola{{ $nombreAdmin ? ', ' . $nombreAdmin : '' }}
        </h2>

        <p style="color: #4b5563; font-size: 14px;">
            Te informamos que tu licencia de PrismaSalud para la IPS/Clínica
            <strong>{{ $licencia->nombre_ips }}</strong> fue <strong>activada</strong>.
        </p>

        <ul style="color: #4b5563; font-size: 14px; line-height: 1.6;">
            <li><strong>NIT:</strong> {{ $licencia->nit }}</li>
            <li><strong>Plan:</strong> {{ $licencia->nombrePlan() }}</li>
            <li><strong>Activa desde:</strong> {{ $licencia->fecha_inicio?->format('d/m/Y') }}</li>
            <li><strong>Vence el:</strong> {{ $licencia->fecha_fin?->format('d/m/Y') }}</li>
            <li><strong>Estado:</strong> Activa</li>
        </ul>

        <p style="color: #4b5563; font-size: 14px;">
            Ya puedes iniciar sesión en el sistema con las credenciales que registraste.
        </p>

        <p style="color: #9ca3af; font-size: 12px; margin-top: 20px;">
            Gracias por confiar en PrismaSalud.
        </p>
    </div>
</body>
</html>
