<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacturaItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'factura_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'subtotal' => 'integer',
    ];

    /**
     * Relación con factura
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación con producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });
    }
}
