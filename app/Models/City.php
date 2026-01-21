<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the state that owns the city.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the delivery slots for the city.
     */
    public function deliverySlots(): HasMany
    {
        return $this->hasMany(DeliverySlot::class);
    }

    /**
     * Get the user addresses for the city.
     */
    public function userAddresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Get the availability zones for the city.
     */
    public function availabilityZones(): HasMany
    {
        return $this->hasMany(AvailabilityZone::class);
    }
}
