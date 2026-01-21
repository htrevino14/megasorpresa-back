<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ReviewDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     summary="Listar reseñas de un producto",
     *     description="Obtiene todas las reseñas aprobadas de un producto específico. Se requiere el parámetro product_id.",
     *     tags={"Reseñas"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="ID del producto para obtener sus reseñas",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de reseñas obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="rating", type="integer", example=5, minimum=1, maximum=5),
     *                     @OA\Property(property="comment", type="string", example="Excelente producto, muy recomendado"),
     *                     @OA\Property(property="is_approved", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Falta el parámetro product_id",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product ID is required")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $productId = $request->query('product_id');

        if (!$productId) {
            return response()->json([
                'message' => 'Product ID is required',
            ], 422);
        }

        $reviews = $this->reviewService->getProductReviews($productId);

        return ReviewResource::collection($reviews);
    }

    /**
     * @OA\Post(
     *     path="/api/reviews",
     *     summary="Crear una nueva reseña",
     *     description="Permite a un usuario autenticado crear una reseña para un producto. La reseña requiere aprobación antes de ser visible públicamente.",
     *     tags={"Reseñas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","rating"},
     *             @OA\Property(property="product_id", type="integer", example=1, description="ID del producto a reseñar"),
     *             @OA\Property(property="rating", type="integer", example=5, minimum=1, maximum=5, description="Calificación del producto (1-5 estrellas)"),
     *             @OA\Property(property="comment", type="string", example="Excelente producto, muy recomendado", maxLength=1000, description="Comentario de la reseña (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reseña creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="rating", type="integer", example=5),
     *                 @OA\Property(property="comment", type="string", example="Excelente producto"),
     *                 @OA\Property(property="is_approved", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(StoreReviewRequest $request)
    {
        $reviewDTO = ReviewDTO::fromRequest($request);
        $review = $this->reviewService->createReview($reviewDTO);

        return (new ReviewResource($review))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/average",
     *     summary="Obtener calificación promedio de un producto",
     *     description="Calcula y retorna la calificación promedio de todas las reseñas aprobadas de un producto específico.",
     *     tags={"Reseñas"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="ID del producto para obtener su calificación promedio",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Calificación promedio obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="average_rating", type="number", format="float", example=4.5, description="Promedio de calificaciones (0-5)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Falta el parámetro product_id",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product ID is required")
     *         )
     *     )
     * )
     */
    public function averageRating(Request $request)
    {
        $productId = $request->query('product_id');

        if (!$productId) {
            return response()->json([
                'message' => 'Product ID is required',
            ], 422);
        }

        $average = $this->reviewService->getProductAverageRating($productId);

        return response()->json([
            'product_id' => $productId,
            'average_rating' => $average,
        ]);
    }
}
