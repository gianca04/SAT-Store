<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'currency_id',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product associated with the price.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the currency associated with the price.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
