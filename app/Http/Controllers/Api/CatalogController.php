<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogIndexRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class CatalogController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/catalog",
     *     summary="Listar catálogo por ciudad",
     *     description="Obtiene un listado paginado de productos activos disponibles en una ciudad específica, con filtro opcional por slug de categoría.",
     *     tags={"Catálogo"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="ID de la ciudad para filtrar productos por disponibilidad",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Slug de la categoría para filtrar productos",
     *         required=false,
     *         @OA\Schema(type="string", example="munecas")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de productos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=12, example=12)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado del catálogo obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación en los parámetros del catálogo"
     *     )
     * )
     * Display a paginated catalog of products available in a city.
     */
    public function index(CatalogIndexRequest $request): AnonymousResourceCollection
    {
        $products = $this->productService->getCatalog(
            cityId: (int) $request->validated('city_id'),
            categorySlug: $request->validated('category'),
            filters: $request->only('per_page'),
        );

        return ProductResource::collection($products);
    }
}
