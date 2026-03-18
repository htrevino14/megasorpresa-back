<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sort_order' => $this->sort_order,
            'links' => $this->links->where('is_active', true)->sortBy('sort_order')->map(fn ($link) => [
                'id' => $link->id,
                'label' => $link->label,
                'url' => $link->url,
                'icon' => $link->icon,
                'open_in_new_tab' => $link->open_in_new_tab,
                'sort_order' => $link->sort_order,
            ])->values(),
        ];
    }
}
