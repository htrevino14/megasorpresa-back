<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsletterCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'slug',
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
     * Get the users subscribed to this newsletter category.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'newsletter_subscriptions')
            ->withPivot('subscribed_at');
    }

    /**
     * Scope a query to only include active newsletter categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
