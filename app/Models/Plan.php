<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'planes';

    protected $fillable = [
        'slug', 'nombre', 'etiqueta', 'descripcion', 'precio_total', 'periodo',
        'ideal', 'beneficios', 'no_incluye', 'cta', 'cta_url', 'icono',
        'clase_icono', 'destacada', 'activo', 'orden',
    ];

    protected $casts = [
        'beneficios' => 'array',
        'no_incluye' => 'array',
        'destacada' => 'boolean',
        'activo' => 'boolean',
    ];

    public function licencias()
    {
        return $this->hasMany(Licencia::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    /**
     * Representa el plan como el arreglo que usaban las vistas cuando los
     * planes vivian en config('planes.items'). Mantiene 'id' como el slug
     * (texto) para no tener que reescribir las vistas ni las rutas.
     */
    public function aArray(): array
    {
        return [
            'id' => $this->slug,
            'plan_id' => $this->id,
            'nombre' => $this->nombre,
            'etiqueta' => $this->etiqueta,
            'descripcion' => $this->descripcion,
            'precio_total' => $this->precio_total,
            'periodo' => $this->periodo,
            'ideal' => $this->ideal,
            'beneficios' => $this->beneficios ?? [],
            'no_incluye' => $this->no_incluye ?? [],
            'cta' => $this->cta,
            'cta_url' => $this->cta_url,
            'icono' => $this->icono,
            'clase_icono' => $this->clase_icono,
            'destacada' => $this->destacada,
        ];
    }

    public static function catalogoArray(): array
    {
        return self::activos()->ordenados()->get()
            ->map(fn (self $plan) => $plan->aArray())
            ->all();
    }

    public static function porSlug(string $slug): ?array
    {
        return collect(self::catalogoArray())->firstWhere('id', $slug);
    }
}