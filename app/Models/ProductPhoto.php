<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'description',
        'is_primary',
        'position',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the photo.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the full URL for the product photo.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->path ? asset('storage/' . $this->path) : null,
        );
    }

    /**
     * Scope a query to only include primary photos.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Cuando se crea una foto, asignar posición automáticamente
        static::creating(function ($photo) {
            if (!$photo->position || !is_numeric($photo->position)) {
                $maxPosition = static::where('product_id', $photo->product_id)->max('position') ?? 0;
                $photo->position = (int) $maxPosition + 1;
            } else {
                $photo->position = (int) $photo->position;
            }

            // Si es la primera foto del producto, marcarla como principal por defecto
            $existingPhotos = static::where('product_id', $photo->product_id)->count();
            if ($existingPhotos === 0 && !isset($photo->is_primary)) {
                $photo->is_primary = true;
            }
        });

        // Cuando se marca como principal, desmarcar las otras
        static::saving(function ($photo) {
            if ($photo->is_primary && $photo->isDirty('is_primary')) {
                static::where('product_id', $photo->product_id)
                    ->where('id', '!=', $photo->id)
                    ->update(['is_primary' => false]);
            }
        });

        // Cuando se elimina una foto principal, promover la primera foto restante
        static::deleted(function ($photo) {
            if ($photo->is_primary) {
                $nextPrimary = static::where('product_id', $photo->product_id)
                    ->orderBy('position', 'asc')
                    ->first();
                
                if ($nextPrimary) {
                    $nextPrimary->update(['is_primary' => true]);
                }
            }
        });
    }
}
