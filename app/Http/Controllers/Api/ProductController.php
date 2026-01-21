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
     * @OA\Get(
     *     path="/api/catalog/products",
     *     summary="Listar productos del catálogo",
     *     description="Obtiene un listado paginado de productos con filtros opcionales por ciudad, categoría y búsqueda. Los productos se filtran por disponibilidad en la ciudad especificada.",
     *     tags={"Catálogo"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="ID de la ciudad para filtrar productos por disponibilidad",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="ID de la categoría para filtrar productos",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar productos por nombre o descripción",
     *         required=false,
     *         @OA\Schema(type="string", example="muñeca")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de productos por página (paginación)",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de productos obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/catalog/products/featured",
     *     summary="Listar productos destacados",
     *     description="Obtiene un listado de productos destacados para una ciudad específica. Útil para mostrar productos en la página principal.",
     *     tags={"Catálogo"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="ID de la ciudad para obtener productos destacados",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número máximo de productos destacados a retornar",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de productos destacados obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     )
     * )
     */
    public function featured(Request $request)
    {
        $cityId = $request->query('city_id', 1);
        $limit = $request->query('limit', 10);

        $products = $this->catalogService->getFeaturedProducts($cityId, $limit);

        return ProductResource::collection($products);
    }

    /**
     * @OA\Get(
     *     path="/api/catalog/products/{id}",
     *     summary="Obtener detalle de un producto",
     *     description="Obtiene la información completa de un producto específico, incluyendo imágenes, categorías y reseñas.",
     *     tags={"Catálogo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del producto obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
    public function show(int $id)
    {
        $product = \App\Models\Product::with(['images', 'categories', 'reviews'])
            ->findOrFail($id);

        return new ProductResource($product);
    }
}
