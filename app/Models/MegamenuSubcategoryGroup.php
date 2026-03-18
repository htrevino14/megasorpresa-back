<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MegamenuSubcategoryGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'megamenu_category_id',
        'title',
        'category_id_destination',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the megamenu category this group belongs to.
     */
    public function megamenuCategory(): BelongsTo
    {
        return $this->belongsTo(MegamenuCategory::class);
    }

    /**
     * Get the catalog category this group points to.
     */
    public function destinationCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id_destination');
    }

    /**
     * Get the subcategory items for this group.
     */
    public function items(): HasMany
    {
        return $this->hasMany(MegamenuSubcategoryItem::class);
    }
}
