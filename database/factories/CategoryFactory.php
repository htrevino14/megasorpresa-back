<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(fake()->numberBetween(1, 3), true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(),
            'image_url' => fake()->optional()->imageUrl(400, 300, 'categories'),
            'parent_id' => null,
        ];
    }

    public function withParent(int $parentId): static
    {
        return $this->state(['parent_id' => $parentId]);
    }
}
