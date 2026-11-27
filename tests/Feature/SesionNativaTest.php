<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\IniciaSesionNativa;
use Tests\TestCase;

class SesionNativaTest extends TestCase
{
    use IniciaSesionNativa, RefreshDatabase;

    /**
     * El administrador con licencia activa entra al catálogo de planes,
     * una ruta permitida por VerificarLicencia aunque no haya pago.
     */
    public function test_administrador_puede_ver_los_planes(): void
    {
        $this->comoAdministrador();

        $this->get(route('admin.planes'))->assertOk();
    }

    /**
     * Un paciente con sesión nativa no puede entrar al panel de administrador.
     */
    public function test_paciente_no_puede_entrar_al_panel_administrador(): void
    {
        $this->comoPaciente();

        $this->get(route('admin.planes'))->assertRedirect('/login');
    }

    /**
     * El dashboard sí pasa por VerificarLicencia: la licencia activa con
     * fecha futura creada por el trait debe dejar pasar al administrador.
     */
    public function test_administrador_con_licencia_activa_ve_el_dashboard(): void
    {
        $this->comoAdministrador();

        $this->get(route('admin.dashboard'))->assertOk();
    }
}
