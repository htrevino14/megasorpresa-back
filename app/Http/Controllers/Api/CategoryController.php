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
     * @OA\Get(
     *     path="/api/catalog/categories",
     *     summary="Listar todas las categorías",
     *     description="Obtiene un listado de todas las categorías de productos disponibles en el catálogo.",
     *     tags={"Catálogo"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de categorías obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->catalogService->getCategories();

        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/catalog/categories/{id}",
     *     summary="Obtener detalle de una categoría con sus productos",
     *     description="Obtiene la información de una categoría específica junto con sus productos asociados. Los productos se pueden filtrar por ciudad y están paginados.",
     *     tags={"Catálogo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la categoría",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="ID de la ciudad para filtrar productos por disponibilidad",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de productos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la categoría con productos obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="category",
     *                 ref="#/components/schemas/Category"
     *             ),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
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
