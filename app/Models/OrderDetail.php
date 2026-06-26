<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recipient_name',
        'recipient_phone',
        'delivery_date',
        'delivery_slot_id',
        'card_message',
        'signature',
        // Snapshot de la dirección de envío al momento de la compra.
        'street',
        'ext_number',
        'neighborhood',
        'dwelling_type',
        'zip_code',
        'city_id',
        'state_id',
        'references',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
        ];
    }

    /**
     * Get the order that owns the detail.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the delivery slot for the order.
     */
    public function deliverySlot(): BelongsTo
    {
        return $this->belongsTo(DeliverySlot::class);
    }

    /**
     * Get the city of the shipping address snapshot.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the state of the shipping address snapshot.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Build a human-readable shipping address from the snapshot.
     *
     * Only includes city/state names when those relations are already loaded,
     * to avoid triggering N+1 queries when listing orders.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->street,
            $this->ext_number,
            $this->neighborhood,
            $this->relationLoaded('city') ? $this->city?->name : null,
            $this->relationLoaded('state') ? $this->state?->name : null,
            $this->zip_code,
        ];

        return implode(', ', array_filter($parts));
    }
}
