<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FooterSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the links for this footer section.
     */
    public function links(): HasMany
    {
        return $this->hasMany(FooterLink::class);
    }

    /**
     * Scope a query to only include active footer sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
