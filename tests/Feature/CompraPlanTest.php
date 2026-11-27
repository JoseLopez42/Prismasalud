<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Licencia;
use App\Models\Transaccion;
use App\Models\Usuario;
use App\Services\Pagos\PasarelaPago;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\Concerns\IniciaSesionNativa;
use Tests\TestCase;

class CompraPlanTest extends TestCase
{
    use IniciaSesionNativa, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Los planes viven en la tabla planes; la migración solo crea la tabla.
        $this->seed(PlanSeeder::class);
    }

    /**
     * Compra nueva, sin sesión: se crean cuenta, IPS, licencia y transacción.
     */
    public function test_compra_nueva_con_tarjeta_credito_crea_plan_empresa_licencia_y_transaccion(): void
    {
        $datos = $this->datosCompra();

        $respuesta = $this->post(route('comprar.procesar', ['plan' => 'gratis']), $datos);

        $respuesta->assertRedirect(route('admin.pago_aceptado'));

        $usuario = Usuario::where('cedula', $datos['cedula'])->firstOrFail();
        $empresa = Empresa::where('nit', $datos['nit'])->firstOrFail();
        $licencia = Licencia::where('usuario_cedula', $usuario->cedula)->firstOrFail();

        $this->assertSame('inactiva', $licencia->estado);

        $transaccion = Transaccion::where('licencia_id', $licencia->id)->firstOrFail();
        $this->assertSame('compra', $transaccion->tipo);
        $this->assertSame('simulada', $transaccion->pasarela);
        $this->assertStringStartsWith('SIM-', $transaccion->referencia_pago);
        $this->assertSame('aprobado', $transaccion->estado);

        $this->assertSame($usuario->cedula, $empresa->usuario_cedula);
    }

    /**
     * PSE necesita referencia: sin ella falla la validación y no se cobra nada.
     */
    public function test_compra_con_pse_sin_referencia_falla_validacion_y_no_crea_transaccion(): void
    {
        $datos = $this->datosCompra([
            'metodo_pago' => 'pse',
        ]);

        $respuesta = $this->post(route('comprar.procesar', ['plan' => 'gratis']), $datos);

        $respuesta->assertSessionHasErrors('referencia');
        $this->assertSame(0, Transaccion::count());
    }

    /**
     * REC-05: aunque lleguen datos de tarjeta "a mano", no se guardan ni se repoblan.
     */
    public function test_campos_de_tarjeta_enviados_a_mano_no_guardan_datos_sensibles(): void
    {
        $numero = '4111111111111111';
        $datos = $this->datosCompra([
            'numero_tarjeta' => $numero,
            'cvv' => '123',
            'vencimiento_tarjeta' => '12/30',
            'nombre_tarjeta' => 'Titular Prueba',
        ]);

        $this->post(route('comprar.procesar', ['plan' => 'gratis']), $datos)
            ->assertRedirect(route('admin.pago_aceptado'));

        $this->assertNumeroNoGuardado($numero);

        $respuesta = $this->post(route('comprar.procesar', ['plan' => 'gratis']), $this->datosCompra([
            'nit' => 'NIT-NO-VALIDO',
            'numero_tarjeta' => $numero,
            'cvv' => '123',
            'vencimiento_tarjeta' => '12/30',
            'nombre_tarjeta' => 'Titular Prueba',
        ]));

        $respuesta->assertSessionHasErrors('nit');
        $respuesta->assertSessionHasInput('nombre_ips');
        $respuesta->assertSessionMissing('_old_input.numero_tarjeta');
        $respuesta->assertSessionMissing('_old_input.cvv');
        $respuesta->assertSessionMissing('_old_input.vencimiento_tarjeta');
        $respuesta->assertSessionMissing('_old_input.nombre_tarjeta');
    }

    /**
     * REC-05: una pasarela no integrada no debe aprobar compras sin cobrar.
     */
    public function test_pasarela_no_soportada_lanza_runtime_exception(): void
    {
        config(['services.pagos.pasarela' => 'wompi']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pasarela de pago no soportada: wompi');

        app(PasarelaPago::class);
    }

    /**
     * Renovación con sesión: se registra como tal y la licencia anterior queda suspendida.
     */
    public function test_renovacion_con_administrador_autenticado_crea_transaccion_de_renovacion(): void
    {
        $administrador = $this->comoAdministrador();
        $licenciaAnterior = $this->licenciaSesion;

        $this->assertNotNull($licenciaAnterior);
        $this->assertSame('activa', $licenciaAnterior->estado);

        $respuesta = $this->post(route('comprar.procesar', ['plan' => 'un-anio']), [
            'metodo_pago' => 'tarjeta_credito',
        ]);

        $respuesta->assertRedirect(route('admin.pago_aceptado'));

        $this->assertSame('suspendida', $licenciaAnterior->fresh()->estado);

        $transaccion = Transaccion::where('tipo', 'renovacion')->firstOrFail();
        $this->assertSame('simulada', $transaccion->pasarela);
        $this->assertSame($administrador->cedula, $transaccion->usuario_cedula);
    }

    /**
     * Datos ficticios para una compra nueva; se pueden sobrescribir por prueba.
     */
    private function datosCompra(array $cambios = []): array
    {
        return array_merge([
            'nombre_ips' => 'IPS Prueba Compra',
            'nit' => fake()->unique()->numerify('900######').'-7',
            'tipo_entidad' => 'IPS',
            'representante_legal' => 'Representante Ficticio',
            'correo_ips' => fake()->unique()->safeEmail(),
            'telefono_ips' => '6081234567',
            'departamento' => 'Tolima',
            'ciudad' => 'Ibagué',
            'direccion' => 'Calle Ficticia 42',
            'cedula' => (int) fake()->unique()->numerify('8########'),
            'telefono' => '3001234567',
            'nombres' => 'Comprador',
            'apellidos' => 'Prueba',
            'correo' => fake()->unique()->safeEmail(),
            'contrasena' => 'Clave@123',
            'contrasena_confirmation' => 'Clave@123',
            'metodo_pago' => 'tarjeta_credito',
        ], $cambios);
    }

    /**
     * Busca el número de prueba en las columnas de texto donde podría quedar.
     */
    private function assertNumeroNoGuardado(string $numero): void
    {
        $like = '%'.$numero.'%';

        $this->assertSame(0, DB::table('transacciones')
            ->where(function ($consulta) use ($like) {
                $consulta->where('referencia_pago', 'like', $like)
                    ->orWhere('tipo', 'like', $like)
                    ->orWhere('metodo_pago', 'like', $like)
                    ->orWhere('pasarela', 'like', $like)
                    ->orWhere('estado', 'like', $like);
            })->count());

        $this->assertSame(0, DB::table('licencias')
            ->where(function ($consulta) use ($like) {
                $consulta->where('nombre_ips', 'like', $like)
                    ->orWhere('nit', 'like', $like)
                    ->orWhere('representante_legal', 'like', $like)
                    ->orWhere('correo_ips', 'like', $like)
                    ->orWhere('telefono_ips', 'like', $like)
                    ->orWhere('departamento', 'like', $like)
                    ->orWhere('ciudad', 'like', $like)
                    ->orWhere('direccion', 'like', $like)
                    ->orWhere('contacto_nombre', 'like', $like)
                    ->orWhere('contacto_email', 'like', $like)
                    ->orWhere('contacto_telefono', 'like', $like)
                    ->orWhere('plan', 'like', $like)
                    ->orWhere('estado', 'like', $like)
                    ->orWhere('notas', 'like', $like);
            })->count());
    }
}
