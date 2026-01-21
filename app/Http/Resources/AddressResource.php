<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'ext_number' => $this->ext_number,
            'neighborhood' => $this->neighborhood,
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                    'state' => $this->city->state->name ?? null,
                ];
            }),
            'zip_code' => $this->zip_code,
            'references' => $this->references,
            'full_address' => $this->full_address,
        ];
    }
}
