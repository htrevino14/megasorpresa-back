<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MegamenuSubcategoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'megamenu_subcategory_group_id',
        'label',
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
     * Get the subcategory group this item belongs to.
     */
    public function subcategoryGroup(): BelongsTo
    {
        return $this->belongsTo(MegamenuSubcategoryGroup::class, 'megamenu_subcategory_group_id');
    }

    /**
     * Get the catalog category this item points to.
     */
    public function destinationCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id_destination');
    }
}
