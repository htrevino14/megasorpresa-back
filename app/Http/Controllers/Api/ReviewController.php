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
     * Display reviews for a product.
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
     * Store a newly created review.
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
     * Get average rating for a product.
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
