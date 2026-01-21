<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'city_id',
    ];

    /**
     * Get the product that owns the availability zone.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the city that owns the availability zone.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
