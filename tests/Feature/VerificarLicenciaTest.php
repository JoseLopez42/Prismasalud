<?php

namespace Tests\Feature;

use App\Http\Middleware\VerificarLicencia;
use App\Models\Admin\Especialidad;
use App\Models\Admin\Medico;
use App\Models\Empresa;
use App\Models\Licencia;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VerificarLicenciaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // unoa_test arranca sin catálogos: los roles se insertan con sus ids
        // fijos para que las FK de usuarios.apuntan a tipos_usuario.id.
        DB::table('tipos_usuario')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Paciente',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Medico',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'Farmaceuta',    'created_at' => now(), 'updated_at' => now()],
        ]);

        // El middleware lee $_SESSION (sesión nativa de PHP, no Auth).
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];

        parent::tearDown();
    }

    /**
     * Simula el login nativo: el middleware decide según estas claves.
     */
    private function iniciarSesion(array $datos): void
    {
        $_SESSION = $datos;
    }

    /**
     * Crea una IPS con su licencia vigente (o en el estado indicado).
     * La empresa nace "inactiva" a propósito: REC-03 NO revisa su estado.
     *
     * forceCreate() se usa porque varios campos obligatorios de la tabla
     * (por ejemplo licencias.plan) no están en el $fillable del modelo.
     *
     * @return array{Empresa, Licencia}
     */
    private function crearIps(string $estadoLicencia = 'activa', ?string $fechaFin = null): array
    {
        $nit = '900123456-' . random_int(100, 999);

        // Primero la empresa, porque licencias.empresa_id es obligatorio.
        $empresa = Empresa::forceCreate([
            'nit'    => $nit,
            'nombre' => 'IPS Prueba REC-03',
            'estado' => 'inactiva',
        ]);

        $licencia = Licencia::forceCreate([
            'empresa_id'          => $empresa->id,
            'nombre_ips'          => 'IPS Prueba REC-03',
            'nit'                 => $nit,
            'representante_legal' => 'Representante Prueba',
            'correo_ips'          => 'ips@rec03.test',
            'telefono_ips'        => '3001112233',
            'plan'                => 'rec03-prueba',
            'fecha_inicio'        => now()->subDay()->toDateString(),
            'fecha_fin'           => $fechaFin ?? now()->addMonth()->toDateString(),
            'estado'              => $estadoLicencia,
        ]);

        // Después la empresa apunta a su licencia vigente.
        $empresa->update(['licencia_id' => $licencia->id]);

        return [$empresa, $licencia];
    }

    private function crearAdministrador(?Licencia $licencia): Usuario
    {
        return Usuario::forceCreate([
            'cedula'          => 900001,
            'nombres'         => 'Admin',
            'apellidos'       => 'Prueba',
            'correo'          => 'admin.rec03@test.test',
            'contrasena'      => bcrypt('clave-prueba'),
            'tipo_usuario_id' => 1,
            'licencia_id'     => $licencia?->id,
        ]);
    }

    private function crearUsuario(int $cedula, int $tipo, ?int $empresaId): Usuario
    {
        return Usuario::forceCreate([
            'cedula'          => $cedula,
            'nombres'         => 'Usuario',
            'apellidos'       => 'Prueba',
            'correo'          => 'usuario' . $cedula . '@rec03.test',
            'contrasena'      => bcrypt('clave-prueba'),
            'tipo_usuario_id' => $tipo,
            'empresa_id'      => $empresaId,
        ]);
    }

    private function crearMedico(int $cedula, ?int $empresaId): Medico
    {
        $especialidad = Especialidad::firstOrCreate([
            'nombre' => 'Especialidad Prueba REC-03',
        ]);

        return Medico::forceCreate([
            'cedula'          => $cedula,
            'empresa_id'      => $empresaId,
            'nombres'         => 'Médico',
            'apellidos'       => 'Prueba',
            'correo'          => 'medico' . $cedula . '@rec03.test',
            'contrasena'      => bcrypt('clave-prueba'),
            'id_especialidad' => $especialidad->id,
        ]);
    }

    /**
     * Crea la persona del rol indicado con la licencia dada y deja lista su
     * sesión nativa. El Administrador usa su propia licencia; los demás
     * roles llegan a ella por empresas.licencia_id.
     */
    private function prepararRol(int $tipo, Empresa $empresa, Licencia $licencia): void
    {
        switch ($tipo) {
            case 1: // Administrador: usuarios.licencia_id
                $this->crearAdministrador($licencia);
                $this->iniciarSesion([
                    'usuario_cedula'  => 900001,
                    'tipo_usuario_id' => 1,
                ]);
                break;

            case 3: // Médico: medico.empresa_id
                $medico = $this->crearMedico(910001, $empresa->id);
                $this->iniciarSesion([
                    'medico_cedula'   => $medico->cedula,
                    'tipo_usuario_id' => 3,
                ]);
                break;

            case 2: // Paciente: usuarios.empresa_id
                $paciente = $this->crearUsuario(920001, 2, $empresa->id);
                $this->iniciarSesion([
                    'usuario_cedula'  => $paciente->cedula,
                    'tipo_usuario_id' => 2,
                ]);
                break;

            case 4: // Farmacéutico: usuarios.empresa_id
                $farmaceuta = $this->crearUsuario(930001, 4, $empresa->id);
                $this->iniciarSesion([
                    'usuario_cedula'  => $farmaceuta->cedula,
                    'tipo_usuario_id' => 4,
                ]);
                break;
        }
    }

    /** El superadministrador no depende de ninguna licencia de IPS. */
    public function test_superadmin_ignora_la_licencia(): void
    {
        // users.id es BIGINT UNSIGNED NOT NULL sin auto_increment: se da explícito.
        $superadmin = User::forceCreate([
            'id'       => 950001,
            'name'     => 'Super Admin REC-03',
            'email'    => 'superadmin.rec03@test.test',
            'password' => bcrypt('clave-prueba'),
            'rol'      => 'superadmin',
        ]);

        $this->crearAdministrador(null);
        $this->iniciarSesion([
            'superadmin_id'   => $superadmin->id,
            'usuario_cedula'  => 900001,
            'tipo_usuario_id' => 1,
        ]);

        $this->get(route('admin.dashboard'))->assertOk();
    }

    /** Rama 1: sin licencia asignada, el Administrador vuelve a "Mi plan". */
    public function test_administrador_sin_licencia_va_a_mi_plan(): void
    {
        $this->crearAdministrador(null);
        $this->iniciarSesion([
            'usuario_cedula'  => 900001,
            'tipo_usuario_id' => 1,
        ]);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.plan'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_NO_ACTIVA);
    }

    /** Rama 1: al vencer, marca la licencia como inactiva y va a "Mi plan". */
    public function test_administrador_con_licencia_vencida_marca_inactiva(): void
    {
        [, $licencia] = $this->crearIps('activa', now()->subDay()->toDateString());
        $this->crearAdministrador($licencia);
        $this->iniciarSesion([
            'usuario_cedula'  => 900001,
            'tipo_usuario_id' => 1,
        ]);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.plan'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_VENCIDA);

        $this->assertSame('inactiva', $licencia->fresh()->estado);
    }

    /** Un médico sin IPS no puede entrar al panel. */
    public function test_medico_sin_empresa_es_bloqueado(): void
    {
        $medico = $this->crearMedico(910001, null);
        $this->iniciarSesion([
            'medico_cedula'   => $medico->cedula,
            'tipo_usuario_id' => 3,
        ]);

        $this->get(route('medico.inicio'))
            ->assertRedirect(route('licencia.bloqueada'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_SIN_IPS);
    }

    /** Un médico de una IPS con licencia activa sí entra a su panel. */
    public function test_medico_con_licencia_activa_puede_entrar(): void
    {
        [$empresa, $licencia] = $this->crearIps('activa');
        $this->prepararRol(3, $empresa, $licencia);

        $this->get(route('medico.inicio'))->assertOk();
    }

    /** Un paciente de una IPS con licencia suspendida queda bloqueado. */
    public function test_paciente_con_licencia_suspendida_es_bloqueado(): void
    {
        [$empresa, $licencia] = $this->crearIps('suspendida');
        $this->prepararRol(2, $empresa, $licencia);

        $this->get(route('usuario.inicio'))
            ->assertRedirect(route('licencia.bloqueada'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_SUSPENDIDA);
    }

    /** Vencida: se avisa, pero NO se reescribe un estado que ya era 'vencida'. */
    public function test_farmaceuta_con_licencia_vencida_no_cambia_el_estado(): void
    {
        [$empresa, $licencia] = $this->crearIps('vencida', now()->subDay()->toDateString());
        $this->prepararRol(4, $empresa, $licencia);

        $this->get(route('farmacia.inicio'))
            ->assertRedirect(route('licencia.bloqueada'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_VENCIDA);

        $this->assertSame('vencida', $licencia->fresh()->estado);
    }

    /** Cualquier estado distinto de 'activa' muestra el mensaje genérico. */
    public function test_licencia_inactiva_muestra_mensaje_no_activa(): void
    {
        [$empresa, $licencia] = $this->crearIps('inactiva');
        $this->prepararRol(2, $empresa, $licencia);

        $this->get(route('usuario.inicio'))
            ->assertRedirect(route('licencia.bloqueada'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_NO_ACTIVA);
    }

    /** Un rol no reconocido tampoco tiene acceso. */
    public function test_tipo_usuario_desconocido_es_bloqueado(): void
    {
        [$empresa] = $this->crearIps('activa');
        $usuario = $this->crearUsuario(940001, 2, $empresa->id);
        $this->iniciarSesion([
            'usuario_cedula'  => $usuario->cedula,
            'tipo_usuario_id' => 99,
        ]);

        $this->get(route('usuario.inicio'))
            ->assertRedirect(route('licencia.bloqueada'))
            ->assertSessionHas('error', VerificarLicencia::MENSAJE_SIN_IPS);
    }

    /** Las rutas permitidas siguen accesibles aunque falte la licencia. */
    public function test_ruta_permitida_no_consulta_licencia(): void
    {
        $this->crearAdministrador(null);
        $this->iniciarSesion([
            'usuario_cedula'  => 900001,
            'tipo_usuario_id' => 1,
        ]);

        $this->get(route('admin.pago_aceptado'))->assertOk();
    }

    /**
     * REC-07: la licencia suspendida bloquea a los cuatro roles.
     * El Administrador mantiene su rama original (admin.plan y mensaje de
     * licencia no activa); los otros tres van a licencia.bloqueada.
     *
     * @return array<string, array{int, string, string, string}>
     */
    public static function rolesConLicenciaSuspendida(): array
    {
        return [
            'administrador' => [1, 'admin.dashboard', 'admin.plan', VerificarLicencia::MENSAJE_NO_ACTIVA],
            'medico'        => [3, 'medico.inicio', 'licencia.bloqueada', VerificarLicencia::MENSAJE_SUSPENDIDA],
            'paciente'      => [2, 'usuario.inicio', 'licencia.bloqueada', VerificarLicencia::MENSAJE_SUSPENDIDA],
            'farmaceuta'    => [4, 'farmacia.inicio', 'licencia.bloqueada', VerificarLicencia::MENSAJE_SUSPENDIDA],
        ];
    }

    #[DataProvider('rolesConLicenciaSuspendida')]
    public function test_licencia_suspendida_bloquea_a_todos_los_roles(
        int $tipo,
        string $ruta,
        string $rutaEsperada,
        string $mensaje
    ): void {
        [$empresa, $licencia] = $this->crearIps('suspendida');
        $this->prepararRol($tipo, $empresa, $licencia);

        $this->get(route($ruta))
            ->assertRedirect(route($rutaEsperada))
            ->assertSessionHas('error', $mensaje);
    }

    /**
     * REC-07: una licencia activa permite el acceso a los cuatro paneles.
     *
     * @return array<string, array{int, string}>
     */
    public static function rolesConLicenciaActiva(): array
    {
        return [
            'administrador' => [1, 'admin.dashboard'],
            'medico'        => [3, 'medico.inicio'],
            'paciente'      => [2, 'usuario.inicio'],
            'farmaceuta'    => [4, 'farmacia.inicio'],
        ];
    }

    #[DataProvider('rolesConLicenciaActiva')]
    public function test_licencia_activa_deja_pasar_a_todos_los_roles(int $tipo, string $ruta): void
    {
        [$empresa, $licencia] = $this->crearIps('activa');
        $this->prepararRol($tipo, $empresa, $licencia);

        $this->get(route($ruta))->assertOk();
    }
}
