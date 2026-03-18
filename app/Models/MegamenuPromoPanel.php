<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MegamenuPromoPanel extends Model
{
    use HasFactory;

    protected $fillable = [
        'megamenu_category_id',
        'badge',
        'title',
        'description',
        'emoji',
        'bg_color',
        'link_text',
        'link_url',
        'image_url',
    ];

    protected function casts(): array
    {
        return [];
    }

    /**
     * Get the megamenu category this promo panel belongs to.
     */
    public function megamenuCategory(): BelongsTo
    {
        return $this->belongsTo(MegamenuCategory::class);
    }
}
