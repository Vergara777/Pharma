<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\LowStockNotification;

class Product extends Model
{

    use HasFactory;

    protected $guarded = [];

    protected $with = ['category', 'supplier'];
    
    protected $appends = ['image_url'];
    
    protected $fillable = [
        'display_no',
        'sku',
        'name',
        'description',
        'image',
        'price',
        'stock',
        'shelf',
        'row',
        'position',
        'expires_at',
        'status',
        'category_id',
        'supplier_id',
        'cost',
        'min_stock',
        'max_stock',
        'unit_name',
        'package_name',
        'units_per_package',
        'price_unit',
        'price_package',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'display_no' => 'integer',
        'units_per_package' => 'integer',
        'expires_at' => 'date',
    ];
    
    protected $attributes = [
        'min_stock' => 5,
        'max_stock' => 100,
        'status' => 'active',
        'unit_name' => 'unidad',
        'units_per_package' => 1,
    ];
    
    /**
     * Get the image URL (handles both file paths and external URLs)
     */
    // public function getImageUrlAttribute()
    // {
    //     if (!$this->image) {
    //         return null;
    //     }
        
    //     // Si es una URL externa, devolverla directamente
    //     if (str_starts_with($this->image, 'http')) {
    //         return $this->image;
    //     }
        
    //     // Si es un archivo local, generar la URL temporal
    //     return \Storage::disk('local')->url($this->image);
    // }
    
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Usa el disco 'public' para que la URL sea accesible desde el navegador
        return \Storage::disk('public')->url($this->image);
    }

    /**
     * Verificar si el producto está activo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    
    /**
     * Verificar si el producto está retirado
     */
    public function isRetired(): bool
    {
        return $this->status === 'retired';
    }
    
    /**
     * Verificar si el producto está por vencer (30 días)
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->diffInDays(now()) <= 30 && $this->expires_at->isFuture();
    }
    
    /**
     * Verificar si el producto está vencido
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }
    
    /**
     * Verificar si el stock está bajo
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
    
    /**
     * Calcular precio por unidad si se vende por presentación
     */
    public function getPricePerUnitAttribute()
    {
        if ($this->price_unit) {
            return $this->price_unit;
        }
        
        if ($this->price_package && $this->units_per_package > 0) {
            return $this->price_package / $this->units_per_package;
        }
        
        return $this->price;
    }
    
    /**
     * Scope para productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope para productos retirados
     */
    public function scopeRetired($query)
    {
        return $query->where('status', 'retired');
    }
    
    /**
     * Scope para productos con stock bajo
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }
    
    /**
     * Scope para productos por vencer
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '>', now())
                    ->where('expires_at', '<', now()->StartOfDay());
    }
    
    /**
     * Scope para productos vencidos
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now()->StartOfDay);
    }
    
    protected static function booted()
    {
        static::saving(function ($product) {
            // Si image_file tiene valor y no es una URL, guardarlo en image
            if (request()->has('image_file') && filled(request('image_file'))) {
                $imageFile = request('image_file');
                // Solo guardar si no es una URL (es un path de archivo)
                if (!str_starts_with($imageFile, 'http')) {
                    $product->image = $imageFile;
                }
            }
            // Si image tiene valor (puede ser URL o path), mantenerlo
            // Filament ya maneja esto automáticamente
        });

        static::saved(function ($product) {
            // Verificar stock después de guardar
            if ($product->wasChanged('stock') || $product->wasChanged('expires_at')) {
                LowStockNotification::checkProduct($product);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class);
    }
}