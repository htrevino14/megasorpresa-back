<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $image_url,
        public ?int $parent_id,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            slug: $request->input('slug'),
            description: $request->input('description'),
            image_url: $request->input('image_url'),
            parent_id: $request->input('parent_id'),
        );
    }
}
