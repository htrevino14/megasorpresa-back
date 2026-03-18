<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementBarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'link_url' => $this->link_url,
            'link_label' => $this->link_label,
            'bg_color' => $this->bg_color,
            'text_color' => $this->text_color,
        ];
    }
}
