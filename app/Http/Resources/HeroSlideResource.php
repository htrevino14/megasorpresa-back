<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroSlideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'cta_text' => $this->cta_text,
            'cta_link' => $this->cta_link,
            'image_url_desktop' => $this->image_url_desktop,
            'image_url_mobile' => $this->image_url_mobile,
            'alt_text' => $this->alt_text,
            'bg_color' => $this->bg_color,
            'sort_order' => $this->sort_order,
        ];
    }
}
