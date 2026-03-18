<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MegamenuCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'subcategory_groups' => $this->subcategoryGroups->sortBy('sort_order')->map(fn ($group) => [
                'id' => $group->id,
                'title' => $group->title,
                'sort_order' => $group->sort_order,
                'items' => $group->items->sortBy('sort_order')->map(fn ($item) => [
                    'id' => $item->id,
                    'label' => $item->label,
                    'sort_order' => $item->sort_order,
                ])->values(),
            ])->values(),
            'promo_panel' => $this->when($this->promoPanel !== null, fn () => [
                'badge' => $this->promoPanel->badge,
                'title' => $this->promoPanel->title,
                'description' => $this->promoPanel->description,
                'emoji' => $this->promoPanel->emoji,
                'bg_color' => $this->promoPanel->bg_color,
                'link_text' => $this->promoPanel->link_text,
                'link_url' => $this->promoPanel->link_url,
                'image_url' => $this->promoPanel->image_url,
            ]),
        ];
    }
}
