<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgeGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'sublabel',
        'slug',
        'bg_color',
        'text_color',
        'category_id_destination',
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
     * Get the catalog category this age group points to.
     */
    public function destinationCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id_destination');
    }

    /**
     * Scope a query to only include active age groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
