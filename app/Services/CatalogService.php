<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogService
{
    /**
     * Get products filtered by city availability.
     */
    public function getProductsByCity(int $cityId, ?int $categoryId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::active()
            ->availableInCity($cityId)
            ->with(['images', 'categories', 'primaryImage']);

        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Search products by name or description.
     */
    public function searchProducts(string $search, ?int $cityId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::active()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->with(['images', 'categories', 'primaryImage']);

        if ($cityId) {
            $query->availableInCity($cityId);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get featured products for a city.
     */
    public function getFeaturedProducts(int $cityId, int $limit = 10): Collection
    {
        return Product::active()
            ->availableInCity($cityId)
            ->with(['images', 'categories', 'primaryImage'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get all categories with their products count.
     */
    public function getCategories(): Collection
    {
        return Category::withCount('products')
            ->with('children')
            ->whereNull('parent_id')
            ->get();
    }

    /**
     * Get a category with its products.
     */
    public function getCategoryWithProducts(int $categoryId, ?int $cityId = null, int $perPage = 15): array
    {
        $category = Category::with('children')->findOrFail($categoryId);

        $query = $category->products()
            ->active()
            ->with(['images', 'categories', 'primaryImage']);

        if ($cityId) {
            $query->availableInCity($cityId);
        }

        return [
            'category' => $category,
            'products' => $query->paginate($perPage),
        ];
    }
}
