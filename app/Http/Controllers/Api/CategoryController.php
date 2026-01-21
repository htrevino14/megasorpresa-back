<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CatalogService $catalogService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = $this->catalogService->getCategories();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category with its products.
     */
    public function show(Request $request, int $id)
    {
        $cityId = $request->query('city_id');
        $perPage = $request->query('per_page', 15);

        $result = $this->catalogService->getCategoryWithProducts($id, $cityId, $perPage);

        return response()->json([
            'category' => new CategoryResource($result['category']),
            'products' => \App\Http\Resources\ProductResource::collection($result['products']),
        ]);
    }
}
