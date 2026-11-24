<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Licencia extends Model
{
    protected $fillable = [
        'nombre_ips', 'nit', 'contacto_nombre', 'contacto_email', 'contacto_telefono',
        'tipo_entidad', 'representante_legal', 'correo_ips', 'telefono_ips',
        'departamento', 'ciudad', 'direccion',
        'plan', 'plan_id', 'fecha_inicio', 'fecha_fin', 'estado', 'notas',
        'usuario_cedula',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // Los usuarios Admin de la IPS que compraron esta licencia
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'licencia_id');
    }

    // El administrador (usuario) que compró esta licencia
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cedula', 'cedula');
    }

    // El plan (de la tabla planes) contratado por esta licencia
    public function planModelo()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    // Historial de pagos/transacciones de esta licencia
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }

    public function diasRestantes(): ?int
    {
        if (!$this->fecha_fin) return null;
        return Carbon::today()->diffInDays($this->fecha_fin, false);
    }

    public function estaPorVencer(int $dias = 15): bool
    {
        $restantes = $this->diasRestantes();
        return $restantes !== null && $restantes >= 0 && $restantes <= $dias;
    }

    public function estaVencida(): bool
    {
        $restantes = $this->diasRestantes();
        return $restantes !== null && $restantes < 0;
    }

    public function nombrePlan(): string
    {
        if ($this->planModelo) {
            return $this->planModelo->nombre;
        }

        $plan = Plan::porSlug($this->plan);
        return $plan['nombre'] ?? $this->plan;
    }

    /**
     * Suspende todas las licencias activas anteriores del mismo administrador
     * o de la misma IPS, y deja sin licencia asignada a los usuarios que
     * apuntaban a ellas. Se usa al renovar y al activar una licencia nueva.
     */
    public static function suspenderAnteriores(self $licencia): void
    {
        $condiciones = [];

        if ($licencia->usuario_cedula) {
            $condiciones[] = ['usuario_cedula', '=', $licencia->usuario_cedula];
        }

        if ($licencia->nit) {
            $condiciones[] = ['nit', '=', $licencia->nit];
        }

        if (! $condiciones) {
            return;
        }

        $anteriores = self::where('id', '!=', $licencia->id)
            ->where('estado', 'activa')
            ->where(function ($q) use ($condiciones) {
                foreach ($condiciones as $i => $condicion) {
                    $i === 0
                        ? $q->where($condicion[0], $condicion[1], $condicion[2])
                        : $q->orWhere($condicion[0], $condicion[1], $condicion[2]);
                }
            })
            ->pluck('id');

        if ($anteriores->isEmpty()) {
            return;
        }

        self::whereIn('id', $anteriores)->update(['estado' => 'suspendida']);

        Usuario::whereIn('licencia_id', $anteriores)->update(['licencia_id' => null]);
    }
}