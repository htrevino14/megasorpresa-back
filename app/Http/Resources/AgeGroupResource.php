<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgeGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'sublabel' => $this->sublabel,
            'slug' => $this->slug,
            'bg_color' => $this->bg_color,
            'text_color' => $this->text_color,
            'sort_order' => $this->sort_order,
            'category_id_destination' => $this->category_id_destination,
        ];
    }
}
