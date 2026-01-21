<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'value',
        'min_purchase',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    /**
     * Check if the coupon is valid.
     */
    public function isValid(): bool
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $subtotal * ($this->value / 100);
        }

        return $this->value;
    }
}
