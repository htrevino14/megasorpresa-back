<?php

namespace App\Services;

use App\Models\NewsletterCategory;
use App\Models\User;

class NewsletterService
{
    /**
     * Subscribe a user to one or more newsletter categories by their slugs.
     * Already-subscribed categories are silently ignored (idempotent).
     *
     * @param  array<string>  $slugs  Category slugs to subscribe to
     * @return array<string> The slugs that were requested
     */
    public function subscribeToCategories(User $user, array $slugs): array
    {
        $categoryIds = NewsletterCategory::active()
            ->whereIn('slug', $slugs)
            ->pluck('id');

        $syncData = $categoryIds->mapWithKeys(fn ($id) => [
            $id => ['subscribed_at' => now()],
        ])->toArray();

        $user->newsletterCategories()->syncWithoutDetaching($syncData);

        return $slugs;
    }
}
