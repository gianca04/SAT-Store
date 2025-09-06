<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'characteristics',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the photos for the product.
     */
    public function photos()
    {
        return $this->hasMany(ProductPhoto::class, 'product_id')->ordered();
    }

    /**
     * Get the primary photo for the product.
     */
    public function primaryPhoto()
    {
        return $this->hasOne(ProductPhoto::class, 'product_id')->where('is_primary', true);
    }

    /**
     * Get the primary photo URL or first photo URL.
     */
    public function getMainPhotoUrlAttribute()
    {
        $primaryPhoto = $this->primaryPhoto;
        if ($primaryPhoto) {
            return $primaryPhoto->image_url;
        }
        
        $firstPhoto = $this->photos()->first();
        return $firstPhoto ? $firstPhoto->image_url : null;
    }

    /**
     * Ensure the product has a primary photo (sets first photo as primary if none exists).
     */
    public function ensurePrimaryPhoto()
    {
        $hasPrimary = $this->photos()->where('is_primary', true)->exists();
        
        if (!$hasPrimary) {
            $firstPhoto = $this->photos()->orderBy('position', 'asc')->first();
            if ($firstPhoto) {
                $firstPhoto->update(['is_primary' => true]);
                return $firstPhoto;
            }
        }
        
        return $this->primaryPhoto;
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}

