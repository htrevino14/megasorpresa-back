<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'parent_id',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category')
            ->withTimestamps();
    }

    /**
     * Get the megamenu categories pointing to this category.
     */
    public function megamenuCategories(): HasMany
    {
        return $this->hasMany(MegamenuCategory::class, 'category_id_destination');
    }

    /**
     * Get the megamenu subcategory groups pointing to this category.
     */
    public function megamenuSubcategoryGroups(): HasMany
    {
        return $this->hasMany(MegamenuSubcategoryGroup::class, 'category_id_destination');
    }

    /**
     * Get the megamenu subcategory items pointing to this category.
     */
    public function megamenuSubcategoryItems(): HasMany
    {
        return $this->hasMany(MegamenuSubcategoryItem::class, 'category_id_destination');
    }

    /**
     * Get the age groups pointing to this category.
     */
    public function ageGroups(): HasMany
    {
        return $this->hasMany(AgeGroup::class, 'category_id_destination');
    }

    /**
     * Get the category carousel items linked to this category.
     */
    public function carouselItems(): HasMany
    {
        return $this->hasMany(CategoryCarouselItem::class);
    }
}
