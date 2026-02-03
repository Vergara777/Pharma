<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ventas extends Model
{
    protected $table = 'ventas';
    
    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'qty',
        'unit_price',
        'total',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'grand_total',
        'status',
        'cancel_reason',
        'cancelled_by',
        'cancelled_at',
        'payment_method_id',
        'payment_reference',
        'amount_received',
        'change_amount',
        'invoice_number',
        'invoice_name',
        'invoice_document',
        'invoice_address',
        'invoice_phone',
        'invoice_email',
        'customer_name',
        'customer_phone',
        'customer_email',
        'user_id',
        'user_role',
        'user_name',
        'cash_session_id',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class, 'venta_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }
}
