<?php

namespace Tests\Feature;

use App\Mail\CodigoRecuperacionMail;
use App\Models\Admin\Beneficiario;
use App\Models\Admin\Especialidad;
use App\Models\Admin\Medico;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * REC-02: seguridad del login y de la recuperación de contraseña.
 * La autenticación sigue siendo nativa ($_SESSION), sin Auth de Laravel.
 */
class Rec02SeguridadLoginTest extends TestCase
{
    use RefreshDatabase;

    private const MENSAJE_CREDENCIALES = 'Cédula o contraseña incorrectas.';
    private const MENSAJE_CODIGO = 'El código es inválido o ya expiró.';
    private const MENSAJE_RECUPERAR = 'Si el correo está registrado, te enviamos un código de recuperación.';

    protected function setUp(): void
    {
        parent::setUp();

        // Los roles se insertan con sus ids fijos por la FK de usuarios.
        DB::table('tipos_usuario')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Paciente',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Medico',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'Farmaceuta',    'created_at' => now(), 'updated_at' => now()],
        ]);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // phpunit.xml usa CACHE_STORE=array: esto evita arrastrar contadores
        // de throttle entre pruebas.
        Cache::flush();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];

        parent::tearDown();
    }

    private function crearUsuario(
        int $cedula,
        int $tipo,
        string $correo,
        bool $activo = true,
        bool $debeCrear = false
    ): Usuario {
        return Usuario::forceCreate([
            'cedula'                => $cedula,
            'nombres'               => 'Usuario',
            'apellidos'             => 'Prueba',
            'correo'                => $correo,
            'contrasena'            => bcrypt('clave-prueba'),
            'tipo_usuario_id'       => $tipo,
            'activo'                => $activo,
            'debe_crear_contrasena' => $debeCrear,
        ]);
    }

    private function crearMedico(int $cedula, string $estado = 'activo'): Medico
    {
        $especialidad = Especialidad::firstOrCreate([
            'nombre' => 'Especialidad Rec02',
        ]);

        return Medico::forceCreate([
            'cedula'          => $cedula,
            'nombres'         => 'Médico',
            'apellidos'       => 'Prueba',
            'correo'          => 'medico'.$cedula.'@rec02.test',
            'contrasena'      => bcrypt('clave-prueba'),
            'estado'          => $estado,
            'id_especialidad' => $especialidad->id,
        ]);
    }

    private function datosLogin(int $cedula, string $contrasena = 'clave-prueba'): array
    {
        return [
            'cedula'     => $cedula,
            'contrasena' => $contrasena,
            'terminos'   => 'on',
        ];
    }

    // ---------- Throttle ----------

    public function test_login_se_bloquea_por_cedula_despues_de_cinco_intentos(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.10']);
        $usuario = $this->crearUsuario(100001, 2, 'login.cedula@rec02.test');

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), $this->datosLogin($usuario->cedula, 'mala'))
                 ->assertSessionHasErrors(['login' => self::MENSAJE_CREDENCIALES]);
        }

        $this->post(route('login.post'), $this->datosLogin($usuario->cedula, 'mala'))
             ->assertStatus(429);

        Cache::flush();

        $this->post(route('login.post'), $this->datosLogin($usuario->cedula))
             ->assertRedirect(route('usuario.inicio'));
    }

    public function test_login_se_bloquea_por_ip_despues_de_treinta_intentos(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.11']);

        // Cédulas distintas para no disparar el límite por cuenta: aquí
        // solo cuenta el límite compartido por IP.
        for ($i = 0; $i < 30; $i++) {
            $this->post(route('login.post'), $this->datosLogin(200000 + $i, 'mala'))
                 ->assertSessionHasErrors(['login' => self::MENSAJE_CREDENCIALES]);
        }

        $this->post(route('login.post'), $this->datosLogin(299999, 'mala'))
             ->assertStatus(429);
    }

    public function test_recuperar_se_bloquea_por_correo_despues_de_tres_intentos(): void
    {
        Mail::fake();
        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.12']);

        $correo = 'recuperar.correo@rec02.test';

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('recuperar.solicitar'), ['correo' => $correo])
                 ->assertRedirect(route('recuperar.verificar.form'));
        }

        $this->post(route('recuperar.solicitar'), ['correo' => $correo])
             ->assertStatus(429);
    }

    public function test_verificar_codigo_se_bloquea_por_correo_de_sesion_y_ip(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.13']);

        // Mezcla de mayúsculas para comprobar que la clave va en minúsculas.
        $this->session(['recuperar_correo' => 'Verificar.Correo@Rec02.test']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('recuperar.verificar'), ['codigo' => '000000'])
                 ->assertSessionHasErrors(['codigo' => self::MENSAJE_CODIGO]);
        }

        $this->post(route('recuperar.verificar'), ['codigo' => '000000'])
             ->assertStatus(429);
    }

    // ---------- Código hasheado e intentos ----------

    public function test_codigo_de_recuperacion_queda_hasheado_y_no_en_texto_plano(): void
    {
        Mail::fake();

        $usuario = $this->crearUsuario(100002, 2, 'codigo.hash@rec02.test');

        $this->post(route('recuperar.solicitar'), ['correo' => $usuario->correo])
             ->assertRedirect(route('recuperar.verificar.form'));

        $codigo = null;
        Mail::assertSent(CodigoRecuperacionMail::class, function ($mensaje) use (&$codigo) {
            $codigo = $mensaje->codigo;

            return true;
        });

        $this->assertNotNull($codigo);
        $usuario = $usuario->fresh();
        $this->assertNotSame($codigo, $usuario->codigo);
        $this->assertTrue(Hash::check($codigo, $usuario->codigo));
        $this->assertSame(0, (int) $usuario->codigo_intentos);
    }

    public function test_verificar_codigo_invalida_el_codigo_tras_cinco_intentos(): void
    {
        Mail::fake();

        $usuario = $this->crearUsuario(100003, 2, 'codigo.intentos@rec02.test');

        $this->post(route('recuperar.solicitar'), ['correo' => $usuario->correo]);

        $codigo = null;
        Mail::assertSent(CodigoRecuperacionMail::class, function ($mensaje) use (&$codigo) {
            $codigo = $mensaje->codigo;

            return true;
        });

        $codigoIncorrecto = $codigo === '000000' ? '111111' : '000000';

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('recuperar.verificar'), ['codigo' => $codigoIncorrecto])
                 ->assertSessionHasErrors(['codigo' => self::MENSAJE_CODIGO]);

            // El throttle de verificación tiene su propia ventana; se limpia
            // para que se compruebe el límite propio del código (5 intentos).
            Cache::flush();
        }

        $this->assertNull($usuario->fresh()->codigo);

        Cache::flush();
        $this->post(route('recuperar.verificar'), ['codigo' => $codigo])
             ->assertSessionHasErrors(['codigo' => self::MENSAJE_CODIGO]);
    }

    public function test_recuperar_devuelve_el_mismo_mensaje_exista_o_no_la_cuenta(): void
    {
        Mail::fake();

        $usuario = $this->crearUsuario(100004, 2, 'mensaje.igual@rec02.test');

        $this->post(route('recuperar.solicitar'), ['correo' => $usuario->correo])
             ->assertRedirect(route('recuperar.verificar.form'))
             ->assertSessionHas('exito', self::MENSAJE_RECUPERAR);

        $this->post(route('recuperar.solicitar'), ['correo' => 'no-existe@rec02.test'])
             ->assertRedirect(route('recuperar.verificar.form'))
             ->assertSessionHas('exito', self::MENSAJE_RECUPERAR);

        Mail::assertSent(CodigoRecuperacionMail::class, 1);
    }

    // ---------- Cuentas inactivas ----------

    public function test_login_rechaza_usuario_inactivo_con_mensaje_generico(): void
    {
        $inactivo = $this->crearUsuario(100005, 2, 'inactivo@rec02.test', false);
        $activo = $this->crearUsuario(100006, 2, 'activo.clave-mala@rec02.test');

        $this->post(route('login.post'), $this->datosLogin($inactivo->cedula))
             ->assertSessionHasErrors(['login' => self::MENSAJE_CREDENCIALES]);

        $this->post(route('login.post'), $this->datosLogin($activo->cedula, 'mala'))
             ->assertSessionHasErrors(['login' => self::MENSAJE_CREDENCIALES]);

        $this->assertArrayNotHasKey('usuario_cedula', $_SESSION);
    }

    public function test_login_rechaza_medico_inactivo_con_mensaje_generico(): void
    {
        $medico = $this->crearMedico(100007, 'inactivo');

        $this->post(route('login.post'), $this->datosLogin($medico->cedula))
             ->assertSessionHasErrors(['login' => self::MENSAJE_CREDENCIALES]);

        $this->assertArrayNotHasKey('medico_cedula', $_SESSION);
    }

    // ---------- Regeneración del id de sesión ----------

    public static function rolesDeUsuarios(): array
    {
        return [
            'administrador' => [1, 'admin.dashboard'],
            'paciente'      => [2, 'usuario.inicio'],
            'farmaceuta'    => [4, 'farmacia.inicio'],
        ];
    }

    #[DataProvider('rolesDeUsuarios')]
    public function test_login_regenera_id_de_sesion_para_usuarios(int $tipo, string $ruta): void
    {
        $cedula = 100030 + $tipo;
        $usuario = $this->crearUsuario($cedula, $tipo, 'regen'.$tipo.'@rec02.test');

        $idAntes = session_id();

        $this->post(route('login.post'), $this->datosLogin($usuario->cedula))
             ->assertRedirect(route($ruta));

        $this->assertNotSame($idAntes, session_id());
        $this->assertSame($usuario->cedula, $_SESSION['usuario_cedula']);
        $this->assertSame($tipo, $_SESSION['tipo_usuario_id']);
    }

    public function test_login_regenera_id_de_sesion_para_medico(): void
    {
        $medico = $this->crearMedico(100008, 'activo');

        $idAntes = session_id();

        $this->post(route('login.post'), $this->datosLogin($medico->cedula))
             ->assertRedirect(route('medico.inicio'));

        $this->assertNotSame($idAntes, session_id());
        $this->assertSame($medico->cedula, $_SESSION['medico_cedula']);
        $this->assertSame(3, $_SESSION['tipo_usuario_id']);
    }

    public function test_login_fallido_no_cambia_el_id_de_sesion(): void
    {
        $idAntes = session_id();

        $this->post(route('login.post'), $this->datosLogin(199999, 'mala'))
             ->assertSessionHasErrors(['login' => self::MENSAJE_CREDENCIALES]);

        $this->assertSame($idAntes, session_id());
    }

    public function test_login_superadmin_regenera_id_al_verificar_el_codigo(): void
    {
        $superadmin = User::forceCreate([
            'id'       => 900001,
            'name'     => 'Super Admin Rec02',
            'email'    => 'superadmin.rec02@test.test',
            'password' => bcrypt('clave-prueba'),
            'rol'      => 'superadmin',
        ]);

        $_SESSION['sa_login'] = [
            'id'      => $superadmin->id,
            'nombre'  => $superadmin->name,
            'email'   => $superadmin->email,
            'hash'    => Hash::make('123456'),
            'expira'  => time() + 600,
            'intentos' => 0,
        ];

        $idAntes = session_id();

        $this->post(route('superadmin.login.codigo.verificar'), ['codigo' => '123456'])
             ->assertRedirect(route('superadmin.dashboard'));

        $this->assertNotSame($idAntes, session_id());
        $this->assertSame($superadmin->id, $_SESSION['superadmin_id']);
        $this->assertArrayNotHasKey('sa_login', $_SESSION);
    }

    public function test_primer_ingreso_regenera_id_al_iniciar_sesion_automaticamente(): void
    {
        $usuario = $this->crearUsuario(100009, 2, 'primer.ingreso@rec02.test', true, true);

        Beneficiario::forceCreate([
            'cc_afiliado'     => $usuario->cedula,
            'tipo_persona'    => 'cotizante',
            'nombres'         => 'Usuario',
            'apellidos'       => 'Prueba',
            'telefono'        => '3001112233',
            'direccion'       => 'Calle 1',
            'correo'          => $usuario->correo,
            'fecha_nacimiento' => '1990-01-01',
        ]);

        $this->session([
            'primer_ingreso_cedula' => $usuario->cedula,
            'primer_ingreso_paso'   => 4,
            'primer_ingreso_correo' => $usuario->correo,
        ]);

        $idAntes = session_id();

        $this->post(route('primer_ingreso.contrasena'), [
            'contrasena'              => 'Clave123!',
            'contrasena_confirmation' => 'Clave123!',
        ])->assertRedirect(route('usuario.inicio'));

        $this->assertNotSame($idAntes, session_id());
        $this->assertSame($usuario->cedula, $_SESSION['usuario_cedula']);
        $this->assertFalse((bool) $usuario->fresh()->debe_crear_contrasena);
    }
}
