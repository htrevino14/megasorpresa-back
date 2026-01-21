<?php

namespace App\Services;

use App\DTOs\ReviewDTO;
use App\Models\Review;

class ReviewService
{
    /**
     * Create a new review.
     */
    public function createReview(ReviewDTO $dto): Review
    {
        return Review::create([
            'product_id' => $dto->product_id,
            'user_id' => $dto->user_id,
            'rating' => $dto->rating,
            'comment' => $dto->comment,
            'is_approved' => $dto->is_approved,
        ]);
    }

    /**
     * Get reviews for a product.
     */
    public function getProductReviews(int $productId, bool $onlyApproved = true)
    {
        $query = Review::where('product_id', $productId)
            ->with('user:id,name');

        if ($onlyApproved) {
            $query->approved();
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get average rating for a product.
     */
    public function getProductAverageRating(int $productId): float
    {
        return Review::where('product_id', $productId)
            ->approved()
            ->avg('rating') ?? 0;
    }
}
