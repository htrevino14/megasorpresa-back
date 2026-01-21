<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliverySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'start_time',
        'end_time',
        'additional_cost',
        'capacity_limit',
    ];

    protected function casts(): array
    {
        return [
            'additional_cost' => 'decimal:2',
            'capacity_limit' => 'integer',
        ];
    }

    /**
     * Get the city that owns the delivery slot.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the order details for the delivery slot.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
