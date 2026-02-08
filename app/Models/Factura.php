<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'invoice_number',
        'cliente_id',
        'user_id',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'integer',
        'tax' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
    ];

    protected $attributes = [
        'status' => 'pending',
        'tax' => 0,
        'discount' => 0,
    ];

    /**
     * Verificar si la factura está pagada
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Verificar si la factura está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si la factura está cancelada
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Verificar si la factura está vencida
     */
    public function isOverdue(): bool
    {
        if (!$this->fecha_vencimiento || $this->isPaid()) {
            return false;
        }

        return $this->fecha_vencimiento->isPast();
    }

    /**
     * Scope para facturas pagadas
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope para facturas pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para facturas canceladas
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope para facturas vencidas
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->whereNotNull('fecha_vencimiento')
                    ->whereDate('fecha_vencimiento', '<', now());
    }

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con usuario que creó la factura
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con items de la factura
     */
    public function items()
    {
        return $this->hasMany(FacturaItem::class);
    }

    /**
     * Generar número de factura automático
     */
    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = static::latest('id')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    protected static function booted()
    {
        static::creating(function ($factura) {
            if (!$factura->invoice_number) {
                $factura->invoice_number = static::generateInvoiceNumber();
            }
            if (!$factura->fecha_emision) {
                $factura->fecha_emision = now();
            }
        });

        static::updating(function ($factura) {
            // Si el estado cambió, podemos registrar el cambio o enviar notificaciones
            if ($factura->isDirty('status')) {
                $oldStatus = $factura->getOriginal('status');
                $newStatus = $factura->status;
                
                // Aquí puedes agregar lógica adicional cuando cambia el estado
                // Por ejemplo, enviar notificación al cliente, registrar en log, etc.
            }
        });
    }
}
