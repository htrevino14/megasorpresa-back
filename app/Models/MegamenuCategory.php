<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MegamenuCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
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
     * Get the catalog category this megamenu category points to.
     */
    public function destinationCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id_destination');
    }

    /**
     * Get the subcategory groups for this megamenu category.
     */
    public function subcategoryGroups(): HasMany
    {
        return $this->hasMany(MegamenuSubcategoryGroup::class);
    }

    /**
     * Get the promo panel for this megamenu category.
     */
    public function promoPanel(): HasOne
    {
        return $this->hasOne(MegamenuPromoPanel::class);
    }

    /**
     * Scope a query to only include active megamenu categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
