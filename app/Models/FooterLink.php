<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_section_id',
        'label',
        'url',
        'icon',
        'open_in_new_tab',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the footer section this link belongs to.
     */
    public function footerSection(): BelongsTo
    {
        return $this->belongsTo(FooterSection::class);
    }

    /**
     * Scope a query to only include active footer links.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
