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
    
    protected $attributes = [
        'stock_minimum' => 20,
        'stock_maximum' => 500,
    ];
    
    /**
     * Get the image URL (handles both file paths and external URLs)
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        // Si es una URL externa, devolverla directamente
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        // Si es un archivo local, generar la URL temporal
        return \Storage::disk('local')->url($this->image);
    }
    
    /**
     * Set stock minimum with default value
     */
    public function setStockMinimumAttribute($value)
    {
        $this->attributes['stock_minimum'] = $value ?? 20;
    }
    
    /**
     * Set stock maximum with default value
     */
    public function setStockMaximumAttribute($value)
    {
        $this->attributes['stock_maximum'] = $value ?? 500;
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
            if ($product->wasChanged('stock') || $product->wasChanged('expiration_date')) {
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