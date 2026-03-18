<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform,
            'url' => $this->url,
            'icon_class' => $this->icon_class,
            'icon_svg' => $this->icon_svg,
            'initial' => $this->initial,
            'sort_order' => $this->sort_order,
        ];
    }
}
