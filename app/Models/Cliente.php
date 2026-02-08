<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Verificar si el cliente está activo
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Scope para clientes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para clientes inactivos
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Relación con facturas
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Relación con ventas
     */
    public function ventas()
    {
        return $this->hasMany(Ventas::class);
    }
}
