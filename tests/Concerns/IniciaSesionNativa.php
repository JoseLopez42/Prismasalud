<?php

namespace Tests\Concerns;

use App\Models\Admin\Especialidad;
use App\Models\Admin\Medico;
use App\Models\Empresa;
use App\Models\Licencia;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/*
 | REC-07: ayudas para probar pantallas protegidas con la sesión nativa de PHP.
 |
 | Uso rápido:
 |   use RefreshDatabase, IniciaSesionNativa;
 |
 |   $admin = $this->comoAdministrador();          // devuelve el Usuario
 |   $this->empresaSesion;                        // Empresa creada con datos ficticios
 |   $this->licenciaSesion;                       // Licencia activa con fecha futura
 |   $this->get(route('admin.dashboard'))->assertOk();
 |
 | Los métodos setUpIniciaSesionNativa() y tearDownIniciaSesionNativa()
 | los llama Laravel automáticamente por el nombre de este trait.
*/
trait IniciaSesionNativa
{
    protected ?Usuario $usuarioSesion = null;

    protected ?Medico $medicoSesion = null;

    protected ?User $superAdminSesion = null;

    protected ?Empresa $empresaSesion = null;

    protected ?Licencia $licenciaSesion = null;

    /**
     * Se ejecuta antes de cada prueba: deja $_SESSION vacío aunque la
     * prueba anterior haya terminado con error, y siembra los roles que
     * exige la llave foránea de usuarios.
     */
    protected function setUpIniciaSesionNativa(): void
    {
        $this->limpiarSesion();
        $this->reiniciarModelosSesion();
        $this->sembrarRolesBase();
    }

    /**
     * Se ejecuta al destruir la aplicación de cada prueba: vuelve a
     * vaciar $_SESSION para que ninguna prueba herede la sesión de otra.
     */
    protected function tearDownIniciaSesionNativa(): void
    {
        $this->limpiarSesion();
        $this->reiniciarModelosSesion();
    }

    /**
     * Administrador (tipo 1). Por defecto se le crea una licencia activa
     * para poder entrar a rutas protegidas por verificarlicencia.
     */
    protected function comoAdministrador(bool $conLicencia = true): Usuario
    {
        $this->limpiarSesion();

        $usuario = $this->crearUsuario(1, 'Admin', 'Sesion');

        if ($conLicencia) {
            $this->crearLicenciaActiva($usuario);
        }

        $this->usuarioSesion = $usuario;
        $_SESSION['usuario_cedula'] = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = 1;

        return $usuario;
    }

    /**
     * Paciente (tipo 2): entra al panel /usuario, no al de administrador.
     */
    protected function comoPaciente(): Usuario
    {
        $this->limpiarSesion();

        $usuario = $this->crearUsuario(2, 'Paciente', 'Sesion');

        $this->usuarioSesion = $usuario;
        $_SESSION['usuario_cedula'] = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = 2;

        return $usuario;
    }

    /**
     * Médico: su sesión usa medico_cedula y su cuenta vive en la tabla medico.
     */
    protected function comoMedico(): Medico
    {
        $this->limpiarSesion();

        $especialidad = Especialidad::create([
            'nombre' => 'Especialidad prueba '.fake()->unique()->numberBetween(1, 999999),
            'descripcion' => 'Especialidad ficticia creada solo para pruebas.',
            'activo' => true,
        ]);

        $medico = Medico::create([
            'cedula' => (int) fake()->unique()->numerify('7########'),
            'nombres' => 'Medico',
            'apellidos' => 'Sesion',
            'correo' => fake()->unique()->safeEmail(),
            'contrasena' => Hash::make('clave-prueba'),
            'estado' => 'activo',
            'id_especialidad' => $especialidad->id,
        ]);

        $this->medicoSesion = $medico;
        $_SESSION['medico_cedula'] = $medico->cedula;
        $_SESSION['medico_nombres'] = $medico->nombres;
        $_SESSION['medico_apellidos'] = $medico->apellidos;
        $_SESSION['medico_foto'] = $medico->foto;
        $_SESSION['tipo_usuario_id'] = 3;

        return $medico;
    }

    /**
     * Farmaceuta (tipo 4): usa usuario_cedula y su propio panel /farmacia.
     */
    protected function comoFarmaceuta(): Usuario
    {
        $this->limpiarSesion();

        $usuario = $this->crearUsuario(4, 'Farmaceuta', 'Sesion');

        $this->usuarioSesion = $usuario;
        $_SESSION['usuario_cedula'] = $usuario->cedula;
        $_SESSION['usuario_nombres'] = $usuario->nombres;
        $_SESSION['tipo_usuario_id'] = 4;

        return $usuario;
    }

    /**
     * Superadministrador: usa users (no usuarios) y EsSuperAdmin exige
     * que el registro tenga rol = superadmin.
     */
    protected function comoSuperAdmin(): User
    {
        $this->limpiarSesion();

        $usuario = User::factory()->create([
            'name' => 'Super Admin Sesion',
            'rol' => 'superadmin',
        ]);

        // El modelo User tiene $incrementing = false; por eso se recarga
        // para tomar el id real que generó MariaDB.
        $usuario = User::where('email', $usuario->email)->firstOrFail();

        $this->superAdminSesion = $usuario;
        $_SESSION['superadmin_id'] = $usuario->id;
        $_SESSION['superadmin_nombre'] = $usuario->name;

        return $usuario;
    }

    /**
     * Crea un usuario de la tabla usuarios con contraseña cifrada.
     */
    private function crearUsuario(int $tipoUsuarioId, string $nombres, string $apellidos): Usuario
    {
        return Usuario::create([
            'cedula' => (int) fake()->unique()->numerify('8########'),
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'correo' => fake()->unique()->safeEmail(),
            'contrasena' => Hash::make('clave-prueba'),
            'tipo_usuario_id' => $tipoUsuarioId,
            'debe_crear_contrasena' => false,
        ]);
    }

    /**
     * Crea empresa y licencia ficticias, y deja las dos propiedades listas
     * para que otras pruebas (REC-01, REC-03) las reutilicen.
     */
    private function crearLicenciaActiva(Usuario $usuario): void
    {
        $nit = fake()->unique()->numerify('999#######');
        $inicio = now()->toDateString();
        $fin = now()->addDays(30)->toDateString();

        $empresa = Empresa::create([
            'usuario_cedula' => $usuario->cedula,
            'nit' => $nit,
            'nombre' => 'IPS Prueba Sesion '.fake()->unique()->numberBetween(1, 999999),
            'tipo_entidad' => 'IPS',
            'representante_legal' => 'Representante Ficticio',
            'correo' => 'ips-sesion@example.test',
            'telefono' => '6000000000',
            'departamento' => 'Antioquia',
            'ciudad' => 'Medellín',
            'direccion' => 'Calle Ficticia 123',
            'plan' => 'gratis',
            'fecha_compra' => $inicio,
            'fecha_expiracion' => $fin,
            'estado' => 'activa',
        ]);

        $licencia = Licencia::create([
            'usuario_cedula' => $usuario->cedula,
            'empresa_id' => $empresa->id,
            'nombre_ips' => $empresa->nombre,
            'nit' => $nit,
            'tipo_entidad' => 'IPS',
            'representante_legal' => 'Representante Ficticio',
            'correo_ips' => 'ips-sesion@example.test',
            'telefono_ips' => '6000000000',
            'departamento' => 'Antioquia',
            'ciudad' => 'Medellín',
            'direccion' => 'Calle Ficticia 123',
            'plan' => 'gratis',
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'estado' => 'activa',
        ]);

        // VerificarLicencia solo lee usuarios.licencia_id; se llena también
        // empresas.licencia_id para dejar el módulo de planes coherente.
        DB::table('usuarios')->where('cedula', $usuario->cedula)
            ->update(['licencia_id' => $licencia->id]);
        DB::table('empresas')->where('id', $empresa->id)
            ->update(['licencia_id' => $licencia->id]);

        $usuario->licencia_id = $licencia->id;
        $empresa->licencia_id = $licencia->id;

        $this->empresaSesion = $empresa;
        $this->licenciaSesion = $licencia;
    }

    /**
     * Los cuatro roles se siembran con ids fijos porque el sistema espera
     * 1 = Administrador, 2 = Paciente, 3 = Médico y 4 = Farmaceuta.
     */
    private function sembrarRolesBase(): void
    {
        $ahora = now();

        DB::table('tipos_usuario')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['id' => 2, 'nombre' => 'Paciente', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['id' => 3, 'nombre' => 'Medico', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['id' => 4, 'nombre' => 'Farmaceuta', 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    private function limpiarSesion(): void
    {
        $_SESSION = [];
    }

    private function reiniciarModelosSesion(): void
    {
        $this->usuarioSesion = null;
        $this->medicoSesion = null;
        $this->superAdminSesion = null;
        $this->empresaSesion = null;
        $this->licenciaSesion = null;
    }
}
