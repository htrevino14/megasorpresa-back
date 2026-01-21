<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ReviewDTO
{
    public function __construct(
        public int $product_id,
        public int $user_id,
        public int $rating,
        public ?string $comment,
        public bool $is_approved = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            product_id: (int) $request->input('product_id'),
            user_id: $request->user()->id,
            rating: (int) $request->input('rating'),
            comment: $request->input('comment'),
            is_approved: false,
        );
    }
}
