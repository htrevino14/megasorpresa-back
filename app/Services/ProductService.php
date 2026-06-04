<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Number of products returned per catalog page.
     */
    private const CATALOG_PER_PAGE = 12;

    /**
     * Get a paginated catalog of products available in a given city,
     * optionally filtered by a category slug.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getCatalog(int $cityId, ?string $categorySlug = null, array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->availableInCity($cityId)
            ->with([
                'primaryImage',
                'categories',
            ]);

        if ($categorySlug !== null) {
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('categories.slug', $categorySlug);
            });
        }

        $perPage = (int) ($filters['per_page'] ?? self::CATALOG_PER_PAGE);

        return $query->paginate($perPage);
    }
}
