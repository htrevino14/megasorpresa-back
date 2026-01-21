<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private CatalogService $catalogService
    ) {}

    /**
     * Display a listing of products with optional filters.
     */
    public function index(Request $request)
    {
        $cityId = $request->query('city_id');
        $categoryId = $request->query('category_id');
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);

        if ($search) {
            $products = $this->catalogService->searchProducts($search, $cityId, $perPage);
        } else {
            $products = $this->catalogService->getProductsByCity($cityId ?? 1, $categoryId, $perPage);
        }

        return ProductResource::collection($products);
    }

    /**
     * Display featured products for a city.
     */
    public function featured(Request $request)
    {
        $cityId = $request->query('city_id', 1);
        $limit = $request->query('limit', 10);

        $products = $this->catalogService->getFeaturedProducts($cityId, $limit);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     */
    public function show(int $id)
    {
        $product = \App\Models\Product::with(['images', 'categories', 'reviews'])
            ->findOrFail($id);

        return new ProductResource($product);
    }
}
